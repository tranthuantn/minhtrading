<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LessonController extends Controller
{
    /**
     * Display a listing of lessons for a course.
     */
    public function index(Course|int $course): View
    {
        $course = $course instanceof Course ? $course : Course::findOrFail($course);

        $lessons = $course->lessons()
            ->withCount('assignments')
            ->orderBy('sort_order', 'asc')
            ->paginate(15);

        return view('admin.lessons.index', compact('course', 'lessons'));
    }

    /**
     * Show the form for creating a new lesson (nested).
     */
    public function create(Request $request, Course|int|null $course = null): View
    {
        $courses = Course::orderBy('sort_order', 'asc')->get();

        $selectedCourse = null;
        if ($course) {
            $selectedCourse = $course instanceof Course ? $course : Course::find($course);
        } elseif ($request->filled('course_id')) {
            $selectedCourse = Course::find($request->input('course_id'));
        }

        if (! $selectedCourse && $courses->count() > 0) {
            $selectedCourse = $courses->first();
        }

        $nextOrder = $selectedCourse ? (($selectedCourse->lessons()->max('sort_order') ?? 0) + 1) : 1;

        return view('admin.lessons.create', [
            'course' => $selectedCourse,
            'courses' => $courses,
            'nextOrder' => $nextOrder,
        ]);
    }

    /**
     * Standalone create route.
     */
    public function createStandalone(Request $request): View
    {
        return $this->create($request);
    }

    /**
     * Store a newly created lesson in storage.
     */
    public function store(Request $request, Course|int|null $course = null): RedirectResponse
    {
        $courseId = $course instanceof Course 
            ? $course->id 
            : ($course ?? $request->input('course_id'));

        $courseObj = Course::findOrFail($courseId);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'max:51200'], // 50MB
            'attachments.*' => ['nullable', 'file', 'max:51200'],
        ]);

        $attachmentsData = [];

        // Single attachment upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('lessons/attachments', 'public');
            $attachmentsData[] = [
                'name' => $file->getClientOriginalName(),
                'url' => $path,
                'size' => $file->getSize(),
                'ext' => $file->getClientOriginalExtension(),
            ];
        }

        // Multiple attachments upload
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('lessons/attachments', 'public');
                $attachmentsData[] = [
                    'name' => $file->getClientOriginalName(),
                    'url' => $path,
                    'size' => $file->getSize(),
                    'ext' => $file->getClientOriginalExtension(),
                ];
            }
        }

        $sortOrder = $validated['sort_order'] ?? (($courseObj->lessons()->max('sort_order') ?? 0) + 1);

        $lesson = $courseObj->lessons()->create([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'attachments' => count($attachmentsData) > 0 ? $attachmentsData : null,
            'sort_order' => $sortOrder,
            'is_published' => $request->boolean('is_published', true),
        ]);

        // Mỗi bài học trong khóa học luôn có bài tập thực hành tương ứng
        $assignmentType = in_array($request->input('assignment_type'), ['analysis', 'backtest', 'trade_entry'])
            ? $request->input('assignment_type')
            : 'analysis';

        $lesson->assignments()->create([
            'title' => $request->input('assignment_title') ?: ('Bài tập: ' . $lesson->title),
            'description' => $request->input('assignment_description') ?: ('Thực hành áp dụng kiến thức từ bài học "' . $lesson->title . '".'),
            'requirements' => $request->input('assignment_requirements') ?: "1. Xem kỹ nội dung lý thuyết và video bài học.\n2. Thực hành phân tích biểu đồ trên TradingView theo nội dung bài học.\n3. Chụp ảnh màn hình đính kèm và nêu rõ lý do nhận định trước khi nộp.",
            'due_date' => $request->filled('assignment_due_date') ? $request->input('assignment_due_date') : now()->addDays(7),
            'type' => $assignmentType,
        ]);

        return redirect()->route('admin.courses.show', $courseObj->id)
            ->with('success', 'Đã thêm bài học "' . $lesson->title . '" và tạo bài tập thực hành tương ứng thành công.');
    }

    /**
     * Standalone store route.
     */
    public function storeStandalone(Request $request): RedirectResponse
    {
        return $this->store($request);
    }

    /**
     * Display the specified lesson.
     */
    public function show(Course|int $course, Lesson|int $lesson): View
    {
        $course = $course instanceof Course ? $course : Course::findOrFail($course);
        $lesson = $lesson instanceof Lesson ? $lesson : Lesson::findOrFail($lesson);

        $lesson->load(['assignments.submissions']);

        return view('admin.lessons.show', compact('course', 'lesson'));
    }

    /**
     * Show the form for editing the specified lesson.
     */
    public function edit(Course|int|null $course, Lesson|int $lesson): View
    {
        $lesson = $lesson instanceof Lesson ? $lesson : Lesson::findOrFail($lesson);
        $course = $lesson->course;
        $courses = Course::orderBy('sort_order', 'asc')->get();

        return view('admin.lessons.edit', compact('course', 'lesson', 'courses'));
    }

    /**
     * Standalone edit route.
     */
    public function editStandalone(int $lesson): View
    {
        $lessonObj = Lesson::findOrFail($lesson);

        return $this->edit($lessonObj->course, $lessonObj);
    }

    /**
     * Update the specified lesson in storage.
     */
    public function update(Request $request, Course|int|null $course, Lesson|int $lesson): RedirectResponse
    {
        $lesson = $lesson instanceof Lesson ? $lesson : Lesson::findOrFail($lesson);
        $courseObj = $lesson->course;

        $validated = $request->validate([
            'course_id' => ['nullable', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'max:51200'],
            'attachments.*' => ['nullable', 'file', 'max:51200'],
        ]);

        $attachmentsData = $lesson->attachments ?? [];

        // Upload single attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('lessons/attachments', 'public');
            $attachmentsData[] = [
                'name' => $file->getClientOriginalName(),
                'url' => $path,
                'size' => $file->getSize(),
                'ext' => $file->getClientOriginalExtension(),
            ];
        }

        // Upload multiple attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('lessons/attachments', 'public');
                $attachmentsData[] = [
                    'name' => $file->getClientOriginalName(),
                    'url' => $path,
                    'size' => $file->getSize(),
                    'ext' => $file->getClientOriginalExtension(),
                ];
            }
        }

        $newCourseId = $validated['course_id'] ?? $lesson->course_id;

        $lesson->update([
            'course_id' => $newCourseId,
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'attachments' => count($attachmentsData) > 0 ? $attachmentsData : null,
            'sort_order' => $validated['sort_order'] ?? $lesson->sort_order,
            'is_published' => $request->boolean('is_published'),
        ]);

        // Đảm bảo bài học luôn có bài tập thực hành tương ứng
        $assignment = $lesson->assignments()->first();
        if ($assignment) {
            $assignmentUpdate = [];
            if ($request->filled('assignment_title')) {
                $assignmentUpdate['title'] = $request->input('assignment_title');
            } elseif (str_starts_with($assignment->title, 'Bài tập: ')) {
                $assignmentUpdate['title'] = 'Bài tập: ' . $lesson->title;
            }
            if ($request->filled('assignment_description')) {
                $assignmentUpdate['description'] = $request->input('assignment_description');
            }
            if ($request->filled('assignment_requirements')) {
                $assignmentUpdate['requirements'] = $request->input('assignment_requirements');
            }
            if ($request->filled('assignment_due_date')) {
                $assignmentUpdate['due_date'] = $request->input('assignment_due_date');
            }
            if ($request->filled('assignment_type') && in_array($request->input('assignment_type'), ['analysis', 'backtest', 'trade_entry'])) {
                $assignmentUpdate['type'] = $request->input('assignment_type');
            }
            if (!empty($assignmentUpdate)) {
                $assignment->update($assignmentUpdate);
            }
        } else {
            $lesson->assignments()->create([
                'title' => $request->input('assignment_title') ?: ('Bài tập: ' . $lesson->title),
                'description' => $request->input('assignment_description') ?: ('Thực hành áp dụng kiến thức từ bài học "' . $lesson->title . '".'),
                'requirements' => $request->input('assignment_requirements') ?: "1. Xem kỹ nội dung lý thuyết và video bài học.\n2. Thực hành phân tích biểu đồ trên TradingView theo nội dung bài học.\n3. Chụp ảnh màn hình đính kèm và nêu rõ lý do nhận định trước khi nộp.",
                'due_date' => $request->filled('assignment_due_date') ? $request->input('assignment_due_date') : now()->addDays(7),
                'type' => in_array($request->input('assignment_type'), ['analysis', 'backtest', 'trade_entry']) ? $request->input('assignment_type') : 'analysis',
            ]);
        }

        return redirect()->route('admin.courses.show', $newCourseId)
            ->with('success', 'Đã cập nhật bài học "' . $lesson->title . '" và đồng bộ bài tập tương ứng thành công.');
    }

    /**
     * Standalone update route.
     */
    public function updateStandalone(Request $request, int $lesson): RedirectResponse
    {
        $lessonObj = Lesson::findOrFail($lesson);

        return $this->update($request, $lessonObj->course, $lessonObj);
    }

    /**
     * Remove the specified lesson from storage.
     */
    public function destroy(Course|int|null $course, Lesson|int $lesson): RedirectResponse
    {
        $lesson = $lesson instanceof Lesson ? $lesson : Lesson::findOrFail($lesson);
        $courseId = $lesson->course_id;
        $title = $lesson->title;

        // Delete attachments files
        if (! empty($lesson->attachments) && is_array($lesson->attachments)) {
            foreach ($lesson->attachments as $att) {
                if (! empty($att['url']) && Storage::disk('public')->exists($att['url'])) {
                    Storage::disk('public')->delete($att['url']);
                }
            }
        }

        $lesson->delete();

        return redirect()->route('admin.courses.show', $courseId)
            ->with('success', 'Đã xóa bài học "' . $title . '" thành công.');
    }

    /**
     * Standalone destroy route.
     */
    public function destroyStandalone(int $lesson): RedirectResponse
    {
        $lessonObj = Lesson::findOrFail($lesson);

        return $this->destroy($lessonObj->course, $lessonObj);
    }

    /**
     * Sắp xếp lại thứ tự các bài học trong một khóa học.
     */
    public function reorder(Request $request, Course|int $course): JsonResponse|RedirectResponse
    {
        $courseObj = $course instanceof Course ? $course : Course::findOrFail($course);

        $request->validate([
            'lessons' => ['required', 'array'],
            'lessons.*.id' => ['required', 'exists:lessons,id'],
            'lessons.*.sort_order' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($request->input('lessons') as $item) {
            Lesson::where('id', $item['id'])
                ->where('course_id', $courseObj->id)
                ->update(['sort_order' => (int) $item['sort_order']]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật thứ tự bài học thành công.',
            ]);
        }

        return back()->with('success', 'Đã lưu thứ tự các bài học thành công.');
    }

    /**
     * Toggle lesson publication status.
     */
    public function toggle(Lesson|int $lesson): RedirectResponse
    {
        $lesson = $lesson instanceof Lesson ? $lesson : Lesson::findOrFail($lesson);
        $lesson->is_published = ! $lesson->is_published;
        $lesson->save();

        $statusText = $lesson->is_published ? 'kích hoạt xuất bản' : 'chuyển về bản nháp';

        return back()->with('success', 'Đã ' . $statusText . ' bài học "' . $lesson->title . '" thành công.');
    }

    /**
     * Upload ảnh trực tiếp từ trình soạn thảo rich text WYSIWYG.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:10240'], // 10MB
        ]);

        $path = $request->file('image')->store('lessons/images', 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'url' => $url,
        ]);
    }
}
