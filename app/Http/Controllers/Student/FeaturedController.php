<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LessonQuestion;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeaturedController extends Controller
{
    /**
     * Display showcase of featured submissions and questions across all courses.
     * All students can view and learn from featured work regardless of course enrollment.
     */
    public function index(Request $request): View
    {
        $courses = Course::published()->orderBy('sort_order', 'asc')->get();

        $tab = $request->input('tab', 'submissions'); // 'submissions' or 'questions'
        $courseId = $request->input('course_id');

        // Query Featured Submissions - open for all students to learn
        $submissionsQuery = Submission::featured()
            ->with([
                'assignment.lesson.course',
                'student',
                'latestReview.reviewer',
            ]);

        // Query Featured Questions - open for all students to learn
        $questionsQuery = LessonQuestion::featured()
            ->with([
                'lesson.course',
                'user',
                'answers.user',
            ])
            ->withCount('answers');

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

        $enrolledCourses = $courses; // Backward compatibility with view variable

        return view('student.featured.index', compact(
            'tab',
            'courseId',
            'courses',
            'enrolledCourses',
            'featuredSubmissions',
            'featuredQuestions',
            'totalFeaturedSubmissions',
            'totalFeaturedQuestions'
        ));
    }
}
