<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LessonQuestion;
use App\Models\QuestionAnswer;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions from students.
     */
    public function index(Request $request): View
    {
        $query = LessonQuestion::with(['lesson.course', 'user', 'answers.user'])
            ->withCount('answers');

        // Filter by course
        if ($request->filled('course_id')) {
            $query->whereHas('lesson', function ($q) use ($request) {
                $q->where('course_id', $request->input('course_id'));
            });
        }

        // Filter by featured status
        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Filter by answered status
        if ($request->filled('status')) {
            if ($request->input('status') === 'answered') {
                $query->has('answers');
            } elseif ($request->input('status') === 'unanswered') {
                $query->doesntHave('answers');
            }
        }

        // Search in title, content, or student name
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $questions = $query->latest()->paginate(15)->withQueryString();
        $courses = Course::orderBy('sort_order', 'asc')->get();

        $stats = [
            'total' => LessonQuestion::count(),
            'unanswered' => LessonQuestion::doesntHave('answers')->count(),
            'featured' => LessonQuestion::featured()->count(),
        ];

        return view('admin.questions.index', compact('questions', 'courses', 'stats'));
    }

    /**
     * Display the specified question with answers and answer form.
     */
    public function show(LessonQuestion $question): View
    {
        $question->load(['lesson.course', 'user', 'answers.user']);

        return view('admin.questions.show', compact('question'));
    }

    /**
     * Submit an answer as instructor / admin.
     */
    public function answer(Request $request, LessonQuestion $question): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'is_featured' => ['nullable', 'boolean'],
            'featured_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('questions/answers', 'public');
                $imageUrls[] = Storage::url($path);
            }
        }

        // Create answer
        QuestionAnswer::create([
            'question_id' => $question->id,
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'images' => ! empty($imageUrls) ? $imageUrls : null,
        ]);

        // If admin checked featured in this answer
        $isFeatured = $request->boolean('is_featured');
        if ($isFeatured && ! $question->is_featured) {
            $question->update([
                'is_featured' => true,
                'featured_note' => $request->input('featured_note') ?: 'Câu hỏi hay, mang tính đúc kết thực chiến cao.',
                'featured_at' => now(),
            ]);
        } elseif (! $isFeatured && $request->has('is_featured') && $question->is_featured) {
            $question->update([
                'is_featured' => false,
                'featured_note' => null,
                'featured_at' => null,
            ]);
        }

        // Send notification to the student
        $featuredExtra = $question->is_featured ? ' Đặc biệt, câu hỏi của bạn đã được Giảng viên đánh giá là CÂU HỎI TIÊU BIỂU của khóa học!' : '';
        UserNotification::create([
            'user_id' => $question->user_id,
            'title' => 'Giảng viên đã giải đáp câu hỏi của bạn!',
            'message' => 'Giảng viên đã phản hồi câu hỏi tại bài học "' . ($question->lesson->title ?? 'Bài học') . '".' . $featuredExtra,
            'type' => 'info',
            'data' => [
                'question_id' => $question->id,
                'lesson_id' => $question->lesson_id,
            ],
            'is_read' => false,
        ]);

        return back()->with('success', 'Đã gửi câu trả lời và thông báo cho học viên thành công.');
    }

    /**
     * Toggle featured status of a question.
     */
    public function toggleFeatured(Request $request, LessonQuestion $question): RedirectResponse
    {
        $newFeatured = ! $question->is_featured;
        $question->update([
            'is_featured' => $newFeatured,
            'featured_note' => $newFeatured ? ($request->input('featured_note') ?? 'Câu hỏi tiêu biểu, hữu ích cho toàn bộ học viên.') : null,
            'featured_at' => $newFeatured ? now() : null,
        ]);

        if ($newFeatured) {
            UserNotification::create([
                'user_id' => $question->user_id,
                'title' => '🌟 Câu hỏi của bạn được chọn làm Câu Hỏi Tiêu Biểu!',
                'message' => 'Câu hỏi của bạn trong bài "' . ($question->lesson->title ?? '') . '" đã được Giảng viên bình chọn vào mục Câu Hỏi Tiêu Biểu để cả lớp cùng học hỏi.',
                'type' => 'announcement',
                'data' => [
                    'question_id' => $question->id,
                    'lesson_id' => $question->lesson_id,
                ],
                'is_read' => false,
            ]);
        }

        $message = $newFeatured
            ? 'Đã chọn câu hỏi này làm Câu Hỏi Tiêu Biểu.'
            : 'Đã hủy nhãn Tiêu Biểu cho câu hỏi này.';

        return back()->with('success', $message);
    }

    /**
     * Remove the specified question.
     */
    public function destroy(LessonQuestion $question): RedirectResponse
    {
        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Đã xóa câu hỏi thành công.');
    }
}

