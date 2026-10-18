<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request): View
    {
        $query = Course::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'published') {
                $query->where('is_published', true);
            } elseif ($status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $courses = $query->withCount(['lessons', 'enrolledStudents'])
            ->orderBy('sort_order', 'asc')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        $students = User::students()->active()->orderBy('name')->get();

        return view('admin.courses.create', compact('students'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:users,id'],
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        } elseif (! empty($validated['thumbnail_url'])) {
            $thumbnailPath = $validated['thumbnail_url'];
        }

        $sortOrder = $validated['sort_order'] ?? ((Course::max('sort_order') ?? 0) + 1);

        $course = Course::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'thumbnail' => $thumbnailPath,
            'sort_order' => $sortOrder,
            'is_published' => $request->boolean('is_published', true),
        ]);

        // Cấp quyền cho học viên nếu có chọn
        if (! empty($validated['student_ids'])) {
            $syncData = [];
            foreach ($validated['student_ids'] as $studentId) {
                $syncData[$studentId] = ['is_active' => true, 'enrolled_at' => now()];
            }
            $course->students()->sync($syncData);
        }

        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', 'Đã tạo khóa học thành công: ' . $course->title);
    }

    /**
     * Display the specified course with its lessons and enrolled students.
     */
    public function show(int $id): View
    {
        $course = Course::with([
            'lessons' => function ($query) {
                $query->withCount('assignments')->orderBy('sort_order', 'asc');
            },
            'enrolledStudents' => function ($query) {
                $query->orderBy('name', 'asc');
            },
        ])->findOrFail($id);

        // Danh sách học viên chưa được cấp quyền để admin chọn thêm
        $enrolledIds = $course->enrolledStudents->pluck('id')->toArray();
        $availableStudents = User::students()
            ->active()
            ->whereNotIn('id', $enrolledIds)
            ->orderBy('name')
            ->get();

        return view('admin.courses.show', compact('course', 'availableStudents'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(int $id): View
    {
        $course = Course::with('enrolledStudents')->findOrFail($id);
        $students = User::students()->active()->orderBy('name')->get();
        $enrolledStudentIds = $course->enrolledStudents->pluck('id')->toArray();

        return view('admin.courses.edit', compact('course', 'students', 'enrolledStudentIds'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:users,id'],
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $course->thumbnail = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        } elseif (! empty($validated['thumbnail_url'])) {
            $course->thumbnail = $validated['thumbnail_url'];
        }

        $course->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $course->sort_order,
            'is_published' => $request->boolean('is_published'),
        ]);

        // Cập nhật danh sách học viên nếu có submit
        if ($request->has('student_ids')) {
            $syncData = [];
            foreach ($request->input('student_ids', []) as $studentId) {
                $syncData[$studentId] = ['is_active' => true, 'enrolled_at' => now()];
            }
            $course->students()->sync($syncData);
        }

        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', 'Thông tin khóa học đã được cập nhật thành công.');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $course = Course::findOrFail($id);
        $title = $course->title;

        if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Đã xóa khóa học "' . $title . '" thành công.');
    }

    /**
     * Toggle course publication status.
     */
    public function toggle(int $id): RedirectResponse
    {
        $course = Course::findOrFail($id);
        $course->is_published = ! $course->is_published;
        $course->save();

        $statusText = $course->is_published ? 'kích hoạt xuất bản' : 'chuyển về bản nháp';

        return back()->with('success', 'Đã ' . $statusText . ' khóa học "' . $course->title . '" thành công.');
    }

    /**
     * Cấp quyền truy cập khóa học cho một hoặc nhiều học viên.
     */
    public function enrollStudent(Request $request, int $id): RedirectResponse
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $userIds = $request->input('user_ids', []);
        $syncData = [];
        foreach ($userIds as $userId) {
            $syncData[$userId] = ['is_active' => true, 'enrolled_at' => now()];
        }

        $course->students()->syncWithoutDetaching($syncData);

        return back()->with('success', 'Đã cấp quyền truy cập khóa học cho ' . count($userIds) . ' học viên thành công.');
    }

    /**
     * Hủy quyền truy cập khóa học của học viên.
     */
    public function unenrollStudent(int $id, int $studentId): RedirectResponse
    {
        $course = Course::findOrFail($id);
        $student = User::findOrFail($studentId);

        $course->students()->detach($studentId);

        return back()->with('success', 'Đã hủy quyền truy cập khóa học của học viên: ' . $student->name);
    }
}
