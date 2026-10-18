<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
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
     * Store a newly created question for a lesson.
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
                    'student_id' => $student->id,
                ],
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Đã gửi câu hỏi thành công! Giảng viên sẽ sớm phản hồi cho bạn.');
    }

    /**
     * Display a question thread.
     */
    public function show(Request $request, LessonQuestion $question): View
    {
        $student = $request->user();
        $question->load(['lesson.course', 'user', 'answers.user']);

        // Học viên phải có quyền truy cập khóa học của bài học đó
        abort_if(
            ! $student->hasCourseAccess($question->lesson->course),
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
        $question->load('lesson.course');

        abort_if(
            ! $student->hasCourseAccess($question->lesson->course),
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
            UserNotification::create([
                'user_id' => $question->user_id,
                'title' => 'Phản hồi mới trong câu hỏi của bạn',
                'message' => $student->name . ' vừa trả lời câu hỏi của bạn tại bài học "' . $question->lesson->title . '".',
                'type' => 'info',
                'data' => [
                    'question_id' => $question->id,
                    'lesson_id' => $question->lesson_id,
                ],
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Đã gửi phản hồi thành công.');
    }
}

