<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments with filters and grouped by course.
     */
    public function index(Request $request): View
    {
        $query = Assignment::with(['lesson.course'])
            ->withCount([
                'submissions',
                'submissions as pending_submissions_count' => function ($q) {
                    $q->where('status', 'pending');
                },
                'submissions as approved_submissions_count' => function ($q) {
                    $q->whereIn('status', ['reviewed', 'approved']);
                },
            ]);

        if ($request->filled('course_id')) {
            $courseId = $request->input('course_id');
            $query->whereHas('lesson', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->input('lesson_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'pending') {
                $query->whereHas('submissions', fn ($q) => $q->where('status', 'pending'));
            } elseif ($status === 'reviewed') {
                $query->whereHas('submissions', fn ($q) => $q->whereIn('status', ['reviewed', 'approved']));
            } elseif ($status === 'no_submissions') {
                $query->doesntHave('submissions');
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $assignments = $query->latest()->paginate(25)->withQueryString();
        $assignmentsByCourse = $assignments->getCollection()->groupBy(fn($a) => $a->lesson?->course_id ?? 0);
        $courses = Course::withCount(['lessons', 'assignments'])->with(['lessons' => fn($q) => $q->orderBy('sort_order')])->orderBy('sort_order')->get();

        return view('admin.assignments.index', compact('assignments', 'assignmentsByCourse', 'courses'));
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create(Request $request): View
    {
        $courses = Course::with(['lessons' => fn($q) => $q->orderBy('sort_order')])->orderBy('sort_order')->get();
        $selectedLessonId = $request->input('lesson_id');
        $existingAssignments = Assignment::with('lesson.course')->latest()->get();

        $selectedTemplate = null;
        if ($request->filled('template_id')) {
            $selectedTemplate = Assignment::find($request->input('template_id'));
        }

        return view('admin.assignments.create', compact('courses', 'selectedLessonId', 'existingAssignments', 'selectedTemplate'));
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lesson_id' => ['required', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'type' => ['required', 'string'],
            'due_date' => ['nullable', 'date'],
        ]);

        $type = match ($validated['type']) {
            'backtest', 'backtest_quiz' => 'backtest',
            'trade_entry', 'trade_review' => 'trade_entry',
            default => 'analysis',
        };

        $assignment = Assignment::create([
            'lesson_id' => $validated['lesson_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'requirements' => $validated['requirements'] ?? null,
            'type' => $type,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return redirect()->route('admin.assignments.show', $assignment->id)
            ->with('success', 'Đã tạo bài tập "' . $assignment->title . '" thành công.');
    }

    /**
     * Display the specified assignment with all student submissions.
     */
    public function show(int $id): View
    {
        $assignment = Assignment::with(['lesson.course'])->findOrFail($id);

        $submissions = $assignment->submissions()
            ->with(['student', 'latestReview.reviewer'])
            ->latest('submitted_at')
            ->paginate(15);

        $stats = [
            'total_submissions' => $assignment->submissions()->count(),
            'pending_review' => $assignment->submissions()->where('status', 'pending')->count(),
            'reviewed' => $assignment->submissions()->whereIn('status', ['reviewed', 'approved', 'rejected'])->count(),
            'average_score' => round((float) ($assignment->submissions()->whereNotNull('score')->avg('score') ?? 0), 1),
        ];

        $courses = Course::with(['lessons' => fn($q) => $q->orderBy('sort_order')])->orderBy('sort_order')->get();

        return view('admin.assignments.show', compact('assignment', 'submissions', 'stats', 'courses'));
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(int $id): View
    {
        $assignment = Assignment::findOrFail($id);
        $courses = Course::with('lessons')->orderBy('sort_order')->get();

        return view('admin.assignments.edit', compact('assignment', 'courses'));
    }

    /**
     * Update the specified assignment in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $assignment = Assignment::findOrFail($id);

        $validated = $request->validate([
            'lesson_id' => ['required', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'type' => ['required', 'string'],
            'due_date' => ['nullable', 'date'],
        ]);

        $type = match ($validated['type']) {
            'backtest', 'backtest_quiz' => 'backtest',
            'trade_entry', 'trade_review' => 'trade_entry',
            default => 'analysis',
        };

        $assignment->update([
            'lesson_id' => $validated['lesson_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'requirements' => $validated['requirements'] ?? null,
            'type' => $type,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return redirect()->route('admin.assignments.show', $assignment->id)
            ->with('success', 'Đã cập nhật bài tập thành công.');
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $assignment = Assignment::findOrFail($id);
        $title = $assignment->title;
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Đã xóa bài tập "' . $title . '" thành công.');
    }

    /**
     * Assignment statistics page: submission rates, average scores, completion rates.
     */
    public function stats(Request $request): View|JsonResponse
    {
        $totalStudents = User::students()->active()->count();

        $assignments = Assignment::with(['lesson.course', 'submissions'])
            ->latest()
            ->get();

        $assignmentStats = $assignments->map(function ($assignment) use ($totalStudents) {
            $totalSubmissions = $assignment->submissions->count();
            $reviewedSubmissions = $assignment->submissions->whereIn('status', ['reviewed', 'approved', 'rejected'])->count();
            $avgScore = $assignment->submissions->whereNotNull('score')->avg('score');
            $avgScore = $avgScore !== null ? round((float) $avgScore, 1) : 0;
            $submissionRate = $totalStudents > 0 ? round(($totalSubmissions / $totalStudents) * 100, 1) : 0;

            return [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'course' => $assignment->lesson?->course?->title ?? 'N/A',
                'type' => $assignment->type,
                'total_submissions' => $totalSubmissions,
                'reviewed_submissions' => $reviewedSubmissions,
                'average_score' => $avgScore,
                'submission_rate' => $submissionRate,
                'max_points' => $assignment->points,
            ];
        });

        // Chart data
        $chartLabels = $assignmentStats->pluck('title')->take(10)->values();
        $chartScores = $assignmentStats->pluck('average_score')->take(10)->values();
        $chartRates = $assignmentStats->pluck('submission_rate')->take(10)->values();

        $chartData = [
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'label' => 'Điểm trung bình',
                    'data' => $chartScores,
                    'backgroundColor' => '#3B82F6',
                    'borderColor' => '#1D4ED8',
                ],
                [
                    'label' => 'Tỷ lệ nộp bài (%)',
                    'data' => $chartRates,
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#047857',
                ],
            ],
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'total_students' => $totalStudents,
                'total_assignments' => $assignments->count(),
                'assignments' => $assignmentStats,
                'chart' => $chartData,
            ]);
        }

        return view('admin.assignments.stats', compact('totalStudents', 'assignmentStats', 'chartData'));
    }

    /**
     * Duplicate an existing assignment to another lesson/course with 1-click.
     */
    public function duplicate(Request $request, Assignment|int $assignment): RedirectResponse
    {
        $assignment = $assignment instanceof Assignment
            ? $assignment
            : Assignment::findOrFail($assignment);

        $validated = $request->validate([
            'lesson_id' => ['required', 'exists:lessons,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
        ]);

        $newTitle = !empty($validated['title']) ? $validated['title'] : $assignment->title;
        $targetLesson = Lesson::with('course')->findOrFail($validated['lesson_id']);

        $newAssignment = Assignment::create([
            'lesson_id' => $targetLesson->id,
            'title' => $newTitle,
            'description' => $assignment->description,
            'requirements' => $assignment->requirements,
            'type' => $assignment->type,
            'due_date' => !empty($validated['due_date']) ? $validated['due_date'] : ($assignment->due_date ? now()->addDays(7) : null),
        ]);

        $courseName = $targetLesson->course->title ?? 'Khóa học';

        return redirect()->route('admin.assignments.show', $newAssignment->id)
            ->with('success', "Đã nhân bản đề bài tập sang bài \"{$targetLesson->title}\" ({$courseName}) thành công!");
    }
}
