<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    /**
     * Display a unified listing of assignments and submissions for the student divided by course.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $userId = $user->id;

        $enrolledCourses = $user->enrolledCourses()
            ->published()
            ->withCount(['lessons', 'assignments'])
            ->orderBy('sort_order', 'asc')
            ->get();

        $enrolledCourseIds = $enrolledCourses->pluck('id')->toArray();

        $selectedCourseId = $request->filled('course_id') ? (int) $request->input('course_id') : null;
        if ($selectedCourseId && ! in_array($selectedCourseId, $enrolledCourseIds)) {
            $selectedCourseId = null;
        }

        $baseQuery = Assignment::active()
            ->whereHas('lesson', function ($q) use ($enrolledCourseIds, $selectedCourseId) {
                if ($selectedCourseId) {
                    $q->where('course_id', $selectedCourseId);
                } else {
                    $q->whereIn('course_id', $enrolledCourseIds);
                }
            });

        $totalCount = (clone $baseQuery)->count();

        $allUserSubmissions = Submission::where('user_id', $userId)
            ->whereHas('assignment.lesson', function ($q) use ($enrolledCourseIds, $selectedCourseId) {
                if ($selectedCourseId) {
                    $q->where('course_id', $selectedCourseId);
                } else {
                    $q->whereIn('course_id', $enrolledCourseIds);
                }
            })
            ->get();

        $submittedAssignmentIds = $allUserSubmissions->pluck('assignment_id')->toArray();

        $pendingCount = $allUserSubmissions->where('status', 'pending')->count();
        $reviewedCount = $allUserSubmissions->whereIn('status', ['reviewed', 'approved'])->count();
        $revisionCount = $allUserSubmissions->where('status', 'revision_needed')->count();
        $notSubmittedCount = max(0, $totalCount - count(array_unique($submittedAssignmentIds)));

        $query = (clone $baseQuery)->with([
            'lesson.course',
            'submissions' => function ($q) use ($userId) {
                $q->where('user_id', $userId)->with('reviews.reviewer')->latest();
            },
        ]);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'not_submitted') {
                $query->whereNotIn('id', $submittedAssignmentIds);
            } elseif ($status === 'submitted') {
                $query->whereIn('id', $submittedAssignmentIds);
            } elseif ($status === 'pending') {
                $query->whereHas('submissions', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('status', 'pending');
                });
            } elseif ($status === 'reviewed') {
                $query->whereHas('submissions', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->whereIn('status', ['reviewed', 'approved']);
                });
            } elseif ($status === 'revision_needed') {
                $query->whereHas('submissions', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('status', 'revision_needed');
                });
            }
        }

        $assignments = $query->orderBy('due_date', 'asc')->get();
        $assignmentsByCourse = $assignments->groupBy(fn ($a) => $a->lesson?->course_id ?? 0);

        // Per course completion stats for student
        $courseStats = [];
        foreach ($enrolledCourses as $course) {
            $courseAssignmentIds = Assignment::whereHas('lesson', fn ($q) => $q->where('course_id', $course->id))->pluck('id');
            $completedCount = Submission::where('user_id', $userId)
                ->whereIn('assignment_id', $courseAssignmentIds)
                ->whereIn('status', ['reviewed', 'approved'])
                ->count();
            $courseStats[$course->id] = [
                'total_assignments' => $courseAssignmentIds->count(),
                'completed' => $completedCount,
                'percent' => $courseAssignmentIds->count() > 0 
                    ? round(($completedCount / $courseAssignmentIds->count()) * 100) 
                    : 0,
            ];
        }

        $stats = [
            'total' => $totalCount,
            'not_submitted' => $notSubmittedCount,
            'submitted' => count(array_unique($submittedAssignmentIds)),
            'pending' => $pendingCount,
            'reviewed' => $reviewedCount,
            'revision_needed' => $revisionCount,
        ];

        return view('student.assignments.index', compact(
            'assignments',
            'assignmentsByCourse',
            'enrolledCourses',
            'selectedCourseId',
            'courseStats',
            'stats'
        ));
    }

    /**
     * Display the specified assignment detail, student submission, and grading feedback.
     */
    public function show(Request $request, Assignment|int $assignment): View
    {
        $assignment = $assignment instanceof Assignment
            ? $assignment
            : Assignment::with('lesson.course')->findOrFail($assignment);

        if ($assignment->lesson && $assignment->lesson->course_id) {
            abort_if(
                ! $request->user()->hasCourseAccess($assignment->lesson->course_id),
                403,
                'Bạn chưa được cấp quyền truy cập khóa học chứa bài tập này.'
            );
        }

        $mySubmission = Submission::with(['reviews.reviewer'])
            ->where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        return view('student.assignments.show', compact('assignment', 'mySubmission'));
    }

    /**
     * Submit or resubmit an assignment.
     */
    public function submit(Request $request, Assignment|int $assignment): RedirectResponse
    {
        $assignment = $assignment instanceof Assignment
            ? $assignment
            : Assignment::findOrFail($assignment);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:10'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,zip,doc,docx'],
            'chart_url' => ['nullable', 'url', 'max:500'],
        ]);

        $userId = $request->user()->id;
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('submissions/attachments', 'public');
        }

        // Check if existing submission exists
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $userId)
            ->first();

        if ($submission) {
            if ($attachmentPath && $submission->attachment_path && Storage::disk('public')->exists($submission->attachment_path)) {
                Storage::disk('public')->delete($submission->attachment_path);
            }

            $submission->update([
                'content' => $validated['content'],
                'attachment_path' => $attachmentPath ?? $submission->attachment_path,
                'chart_url' => $validated['chart_url'] ?? $submission->chart_url,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            $message = 'Bài tập đã được nộp lại thành công! Đang chờ Giảng viên chấm điểm.';
        } else {
            $submission = Submission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $userId,
                'user_id' => $userId,
                'content' => $validated['content'],
                'attachment_path' => $attachmentPath,
                'chart_url' => $validated['chart_url'] ?? null,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            $message = 'Nộp bài tập "' . $assignment->title . '" thành công! Giảng viên sẽ chấm điểm trong thời gian sớm nhất.';
        }

        return redirect()->route('student.assignments.show', $assignment->id)->with('success', $message);
    }

    /**
     * Redirect to unified assignments & submissions list.
     */
    public function mySubmissions(Request $request): RedirectResponse
    {
        return redirect()->route('student.assignments.index', ['status' => 'submitted']);
    }

    /**
     * Show a specific submission - redirects to unified assignment page.
     */
    public function showSubmission(int $id): RedirectResponse
    {
        $submission = Submission::where('user_id', auth()->id())->findOrFail($id);

        return redirect()->route('student.assignments.show', $submission->assignment_id);
    }
}
