<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of courses that the student is permitted to access.
     */
    public function index(Request $request): View
    {
        $student = $request->user();

        // Học viên chỉ xem được các khóa học mà mình được cấp quyền
        $query = $student->enrolledCourses()
            ->published()
            ->withCount(['lessons' => function ($q) {
                $q->published();
            }]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('courses.title', 'like', "%{$search}%")
                    ->orWhere('courses.description', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('courses.sort_order', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('student.courses.index', compact('courses'));
    }

    /**
     * Display the specified course with its published lessons if student has access.
     */
    public function show(Request $request, Course|int $course): View
    {
        $course = $course instanceof Course
            ? $course
            : Course::published()->findOrFail($course);

        abort_if(! $course->is_published, 404, 'Khóa học không tồn tại hoặc chưa được xuất bản.');

        // Kiểm tra quyền truy cập của học viên
        abort_if(
            ! $request->user()->hasCourseAccess($course), 
            403, 
            'Bạn chưa được cấp quyền truy cập khóa học này. Vui lòng liên hệ Giảng viên để được mở quyền học.'
        );

        $lessons = $course->lessons()
            ->published()
            ->withCount('assignments')
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('student.courses.show', compact('course', 'lessons'));
    }

    /**
     * Display the specified lesson content if student has access to course.
     */
    public function lesson(Request $request, Course|int $course, Lesson|int $lesson): View
    {
        $course = $course instanceof Course
            ? $course
            : Course::published()->findOrFail($course);

        $lesson = $lesson instanceof Lesson
            ? $lesson
            : Lesson::published()->where('course_id', $course->id)->findOrFail($lesson);

        abort_if(! $course->is_published || ! $lesson->is_published, 404, 'Bài học không khả dụng.');
        abort_if($lesson->course_id !== $course->id, 404, 'Bài học không thuộc khóa học này.');

        // Kiểm tra quyền truy cập của học viên đối với khóa học này
        abort_if(
            ! $request->user()->hasCourseAccess($course), 
            403, 
            'Bạn chưa được cấp quyền truy cập bài học trong khóa học này. Vui lòng liên hệ Giảng viên.'
        );

        // Previous and Next lessons
        $allLessons = $course->lessons()->published()->orderBy('sort_order', 'asc')->get();
        $currentIndex = $allLessons->search(fn ($l) => $l->id === $lesson->id);

        $prevLesson = $currentIndex > 0 ? $allLessons->get($currentIndex - 1) : null;
        $nextLesson = $currentIndex !== false && $currentIndex < $allLessons->count() - 1 ? $allLessons->get($currentIndex + 1) : null;

        // Assignments in this lesson with student submission status
        $assignments = $lesson->assignments()
            ->active()
            ->with(['submissions' => function ($q) use ($request) {
                $q->where('user_id', $request->user()->id)->latest();
            }])
            ->get();

        // Questions in this lesson
        $questions = $lesson->questions()
            ->with(['user', 'answers.user'])
            ->latest()
            ->get();

        return view('student.courses.lesson', compact(
            'course',
            'lesson',
            'allLessons',
            'prevLesson',
            'nextLesson',
            'assignments',
            'questions'
        ));
    }
}
