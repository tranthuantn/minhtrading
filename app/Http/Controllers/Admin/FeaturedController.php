<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LessonQuestion;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeaturedController extends Controller
{
    /**
     * Display showcase of all featured submissions and questions for admin management.
     */
    public function index(Request $request): View
    {
        $courses = Course::orderBy('sort_order', 'asc')->get();
        $tab = $request->input('tab', 'submissions'); // 'submissions' or 'questions'
        $courseId = $request->input('course_id');

        // Query Featured Submissions
        $submissionsQuery = Submission::featured()
            ->with([
                'assignment.lesson.course',
                'student',
                'latestReview.reviewer',
            ]);

        // Query Featured Questions
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

        return view('admin.featured.index', compact(
            'tab',
            'courseId',
            'courses',
            'featuredSubmissions',
            'featuredQuestions',
            'totalFeaturedSubmissions',
            'totalFeaturedQuestions'
        ));
    }
}

