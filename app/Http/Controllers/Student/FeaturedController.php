<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LessonQuestion;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeaturedController extends Controller
{
    /**
     * Display showcase of featured submissions and questions for enrolled courses.
     */
    public function index(Request $request): View
    {
        $student = $request->user();
        $enrolledCourses = $student->enrolledCourses()->published()->orderBy('courses.sort_order', 'asc')->get();
        $enrolledCourseIds = $enrolledCourses->pluck('id')->toArray();

        $tab = $request->input('tab', 'submissions'); // 'submissions' or 'questions'
        $courseId = $request->input('course_id');

        // Query Featured Submissions
        $submissionsQuery = Submission::featured()
            ->with([
                'assignment.lesson.course',
                'student',
                'latestReview.reviewer',
            ])
            ->whereHas('assignment.lesson', function ($q) use ($enrolledCourseIds) {
                $q->whereIn('course_id', $enrolledCourseIds);
            });

        // Query Featured Questions
        $questionsQuery = LessonQuestion::featured()
            ->with([
                'lesson.course',
                'user',
                'answers.user',
            ])
            ->withCount('answers')
            ->whereHas('lesson', function ($q) use ($enrolledCourseIds) {
                $q->whereIn('course_id', $enrolledCourseIds);
            });

        if ($courseId) {
            $submissionsQuery->whereHas('assignment.lesson', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });

            $questionsQuery->whereHas('lesson', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $totalFeaturedSubmissions = (clone $submissionsQuery)->count();
        $totalFeaturedQuestions = (clone $questionsQuery)->count();

        if ($tab === 'questions') {
            $featuredQuestions = $questionsQuery->latest('featured_at')->paginate(12)->withQueryString();
            $featuredSubmissions = collect();
        } else {
            $featuredSubmissions = $submissionsQuery->latest('featured_at')->paginate(12)->withQueryString();
            $featuredQuestions = collect();
        }

        return view('student.featured.index', compact(
            'tab',
            'courseId',
            'enrolledCourses',
            'featuredSubmissions',
            'featuredQuestions',
            'totalFeaturedSubmissions',
            'totalFeaturedQuestions'
        ));
    }
}

