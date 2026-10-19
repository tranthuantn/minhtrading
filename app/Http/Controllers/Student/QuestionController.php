<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonQuestion;
use App\Models\QuestionAnswer;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * Store a question directly from within a specific lesson page.
     */
    public function store(Request $request, Lesson $lesson): RedirectResponse
    {
        $student = $request->user();

        // Kiểm tra quyền truy cập khóa học
        abort_if(
            ! $student->hasCourseAccess($lesson->course),
            403,
            'Bạn không có quyền đặt câu hỏi cho bài học này.'
        );

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
        ]);

        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('questions/images', 'public');
                $imageUrls[] = Storage::url($path);
            }
        }

        $question = LessonQuestion::create([
            'user_id' => $student->id,
            'course_id' => $lesson->course_id,
            'lesson_id' => $lesson->id,
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'images' => ! empty($imageUrls) ? $imageUrls : null,
            'is_featured' => false,
        ]);

        // Thông báo cho Giảng viên / Admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            UserNotification::create([
                'user_id' => $admin->id,
                'title' => 'Câu hỏi mới từ học viên: ' . $student->name,
                'message' => 'Học viên ' . $student->name . ' vừa đặt câu hỏi trong bài "' . $lesson->title . '" (' . ($lesson->course->title ?? '') . ').',
                'type' => 'info',
                'data' => [
                    'question_id' => $question->id,
                    'lesson_id' => $lesson->id,
                    'course_id' => $lesson->course_id,
                    'student_id' => $student->id,
                ],
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Đã gửi câu hỏi thành công! Giảng viên sẽ sớm phản hồi cho bạn.');
    }

    /**
     * Store a question from the Q&A hub (either in a lesson or outside lesson).
     */
    public function storeGeneral(Request $request): RedirectResponse
    {
        $student = $request->user();

        $validated = $request->validate([
            'scope' => ['required', 'in:lesson,outside'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
        ]);

        $lessonId = null;
        $courseId = null;
        $contextInfo = '';

        if ($validated['scope'] === 'lesson') {
            if (empty($validated['lesson_id'])) {
                return back()->withErrors(['lesson_id' => 'Vui lòng chọn bài học cho câu hỏi.'])->withInput();
            }
            $lesson = Lesson::with('course')->findOrFail($validated['lesson_id']);
            abort_if(! $student->hasCourseAccess($lesson->course), 403, 'Bạn không có quyền đặt câu hỏi trong khóa học này.');
            $lessonId = $lesson->id;
            $courseId = $lesson->course_id;
            $contextInfo = 'trong bài học "' . $lesson->title . '" (' . ($lesson->course->title ?? '') . ')';
        } else {
            // Ngoài bài học
            if (! empty($validated['course_id'])) {
                $course = Course::findOrFail($validated['course_id']);
                abort_if(! $student->hasCourseAccess($course), 403, 'Bạn không có quyền đặt câu hỏi trong khóa học này.');
                $courseId = $course->id;
                $contextInfo = 'ngoài bài học (Khóa: ' . $course->title . ')';
            } else {
                $contextInfo = 'ngoài bài học (Hỏi đáp chung)';
            }
        }

        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('questions/images', 'public');
                $imageUrls[] = Storage::url($path);
            }
        }

        $question = LessonQuestion::create([
            'user_id' => $student->id,
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'images' => ! empty($imageUrls) ? $imageUrls : null,
            'is_featured' => false,
        ]);

        // Thông báo cho Giảng viên / Admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            UserNotification::create([
                'user_id' => $admin->id,
                'title' => 'Câu hỏi mới từ ' . $student->name,
                'message' => 'Học viên ' . $student->name . ' vừa đặt câu hỏi ' . $contextInfo . '.',
                'type' => 'info',
                'data' => [
                    'question_id' => $question->id,
                    'lesson_id' => $lessonId,
                    'course_id' => $courseId,
                    'student_id' => $student->id,
                ],
                'is_read' => false,
            ]);
        }

        return redirect()->route('student.assignments.index', ['tab' => 'questions'])
            ->with('success', 'Đã gửi câu hỏi thắc mắc thành công! Giảng viên sẽ sớm phản hồi cho bạn.');
    }

    /**
     * Display a question thread.
     */
    public function show(Request $request, LessonQuestion $question): View
    {
        $student = $request->user();
        $question->load(['lesson.course', 'course', 'user', 'answers.user']);

        // Cho phép xem nếu:
        // 1. Là câu hỏi tiêu biểu
        // 2. Do chính học viên đó tạo ra
        // 3. Ngoài bài học và không gắn khóa (câu hỏi chung)
        // 4. Có gắn khóa/bài học mà học viên có quyền truy cập
        $hasAccess = (bool) $question->is_featured 
            || $question->user_id === $student->id
            || (! $question->lesson_id && ! $question->course_id)
            || ($question->course && $student->hasCourseAccess($question->course))
            || ($question->lesson && $student->hasCourseAccess($question->lesson->course));

        abort_if(
            ! $hasAccess,
            403,
            'Bạn không có quyền xem câu hỏi này.'
        );

        return view('student.questions.show', compact('question'));
    }

    /**
     * Reply to a question.
     */
    public function reply(Request $request, LessonQuestion $question): RedirectResponse
    {
        $student = $request->user();
        $question->load(['lesson.course', 'course']);

        $hasAccess = (bool) $question->is_featured 
            || $question->user_id === $student->id
            || (! $question->lesson_id && ! $question->course_id)
            || ($question->course && $student->hasCourseAccess($question->course))
            || ($question->lesson && $student->hasCourseAccess($question->lesson->course));

        abort_if(
            ! $hasAccess,
            403,
            'Bạn không có quyền thảo luận trong câu hỏi này.'
        );

        $validated = $request->validate([
            'content' => ['required', 'string'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
        ]);

        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('questions/answers', 'public');
                $imageUrls[] = Storage::url($path);
            }
        }

        QuestionAnswer::create([
            'question_id' => $question->id,
            'user_id' => $student->id,
            'content' => $validated['content'],
            'images' => ! empty($imageUrls) ? $imageUrls : null,
        ]);

        // Nếu người trả lời không phải là người đặt câu hỏi, thông báo cho người đặt câu hỏi
        if ($question->user_id !== $student->id) {
            $contextText = $question->lesson 
                ? 'tại bài học "' . $question->lesson->title . '"'
                : ($question->course ? 'thuộc khóa "' . $question->course->title . '"' : '(câu hỏi ngoài bài học)');

            UserNotification::create([
                'user_id' => $question->user_id,
                'title' => 'Phản hồi mới trong câu hỏi của bạn',
                'message' => $student->name . ' vừa trả lời câu hỏi của bạn ' . $contextText . '.',
                'type' => 'info',
                'data' => [
                    'question_id' => $question->id,
                    'lesson_id' => $question->lesson_id,
                    'course_id' => $question->course_id,
                ],
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Đã gửi phản hồi thành công.');
    }
}
