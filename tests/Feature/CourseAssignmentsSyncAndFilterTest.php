<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseAssignmentsSyncAndFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected Course $course1;
    protected Course $course2;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->course1 = Course::create([
            'title' => 'Khóa học Price Action Cơ Bản',
            'description' => 'Mô tả khóa 1',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->course2 = Course::create([
            'title' => 'Khóa học SMC Chuyên Sâu',
            'description' => 'Mô tả khóa 2',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        // Student enrolled in both courses
        $this->course1->students()->attach($this->student->id, ['is_active' => true, 'enrolled_at' => now()]);
        $this->course2->students()->attach($this->student->id, ['is_active' => true, 'enrolled_at' => now()]);
    }

    public function test_creating_a_lesson_does_not_automatically_create_assignment(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.courses.lessons.store', $this->course1->id), [
            'title' => 'Bài 1: Xác định cấu trúc thị trường',
            'content' => '<p>Hướng dẫn xác định đỉnh đáy</p>',
            'sort_order' => 1,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.courses.show', $this->course1->id));
        $this->assertDatabaseHas('lessons', [
            'course_id' => $this->course1->id,
            'title' => 'Bài 1: Xác định cấu trúc thị trường',
        ]);

        $lesson = Lesson::where('title', 'Bài 1: Xác định cấu trúc thị trường')->first();
        $this->assertNotNull($lesson);

        // Verify NO assignment was created automatically
        $this->assertEquals(0, $lesson->assignments()->count());
    }

    public function test_admin_can_create_assignment_independently(): void
    {
        $lesson = $this->course1->lessons()->create([
            'title' => 'Bài 1: Xác định cấu trúc thị trường',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.assignments.store'), [
            'lesson_id' => $lesson->id,
            'title' => 'Bài tập thực hành độc lập',
            'type' => 'analysis',
            'description' => 'Mô tả bài tập do admin tự tạo',
            'requirements' => 'Vẽ 3 con sóng đẩy và 3 con sóng hồi',
            'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
        ]);

        $assignment = Assignment::where('title', 'Bài tập thực hành độc lập')->first();
        $this->assertNotNull($assignment);
        $response->assertRedirect(route('admin.assignments.show', $assignment->id));
        $this->assertDatabaseHas('assignments', [
            'lesson_id' => $lesson->id,
            'title' => 'Bài tập thực hành độc lập',
            'type' => 'analysis',
        ]);
    }

    public function test_updating_a_lesson_does_not_alter_assignments(): void
    {
        $lesson = $this->course1->lessons()->create([
            'title' => 'Bài 2: Vùng Supply Demand',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $assignment = $lesson->assignments()->create([
            'title' => 'Bài tập gốc không bị can thiệp',
            'description' => 'Mô tả ban đầu',
            'type' => 'analysis',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.courses.lessons.update', [$this->course1->id, $lesson->id]), [
            'title' => 'Bài 2: Các vùng Cung Cầu quan trọng',
            'sort_order' => 2,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.courses.show', $this->course1->id));

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Bài tập gốc không bị can thiệp',
        ]);
    }

    public function test_student_assignments_index_displays_assignments_divided_by_course(): void
    {
        $lesson1 = $this->course1->lessons()->create([
            'title' => 'Bài 1 Khóa 1',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $assignment1 = $lesson1->assignments()->create([
            'title' => 'Đề bài tập số 1 Khóa 1',
            'type' => 'analysis',
            'due_date' => now()->addDays(5),
        ]);

        $lesson2 = $this->course2->lessons()->create([
            'title' => 'Bài 1 Khóa 2',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $assignment2 = $lesson2->assignments()->create([
            'title' => 'Đề bài tập số 1 Khóa 2',
            'type' => 'trade_entry',
            'due_date' => now()->addDays(7),
        ]);

        $response = $this->actingAs($this->student)->get(route('student.assignments.index'));

        $response->assertStatus(200);
        $response->assertSee('Khóa học Price Action Cơ Bản');
        $response->assertSee('Khóa học SMC Chuyên Sâu');
        $response->assertSee('Đề bài tập số 1 Khóa 1');
        $response->assertSee('Đề bài tập số 1 Khóa 2');
    }

    public function test_student_assignments_index_filters_by_course(): void
    {
        $lesson1 = $this->course1->lessons()->create([
            'title' => 'Bài 1 Khóa 1',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $lesson1->assignments()->create([
            'title' => 'Đề bài tập Price Action',
            'type' => 'analysis',
        ]);

        $lesson2 = $this->course2->lessons()->create([
            'title' => 'Bài 1 Khóa 2',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $lesson2->assignments()->create([
            'title' => 'Đề bài tập SMC',
            'type' => 'trade_entry',
        ]);

        // Filter only Course 1
        $response = $this->actingAs($this->student)->get(route('student.assignments.index', ['course_id' => $this->course1->id]));
        $response->assertStatus(200);
        $response->assertSee('Đề bài tập Price Action');
        $response->assertDontSee('Đề bài tập SMC');

        // Filter only Course 2
        $response2 = $this->actingAs($this->student)->get(route('student.assignments.index', ['course_id' => $this->course2->id]));
        $response2->assertStatus(200);
        $response2->assertSee('Đề bài tập SMC');
        $response2->assertDontSee('Đề bài tập Price Action');
    }

    public function test_student_assignments_index_filters_by_status(): void
    {
        $lesson1 = $this->course1->lessons()->create(['title' => 'Bài 1', 'sort_order' => 1, 'is_published' => true]);
        $assignment1 = $lesson1->assignments()->create(['title' => 'Bài tập đã nộp', 'type' => 'analysis']);

        $lesson2 = $this->course1->lessons()->create(['title' => 'Bài 2', 'sort_order' => 2, 'is_published' => true]);
        $assignment2 = $lesson2->assignments()->create(['title' => 'Bài tập chưa nộp', 'type' => 'analysis']);

        Submission::create([
            'assignment_id' => $assignment1->id,
            'student_id' => $this->student->id,
            'content' => 'Nội dung bài làm',
            'status' => 'pending',
        ]);

        // Filter pending
        $pendingResp = $this->actingAs($this->student)->get(route('student.assignments.index', ['status' => 'pending']));
        $pendingResp->assertStatus(200);
        $pendingResp->assertSee('Bài tập đã nộp');
        $pendingResp->assertDontSee('Bài tập chưa nộp');

        // Filter not submitted
        $notSubResp = $this->actingAs($this->student)->get(route('student.assignments.index', ['status' => 'not_submitted']));
        $notSubResp->assertStatus(200);
        $notSubResp->assertSee('Bài tập chưa nộp');
        $notSubResp->assertDontSee('Bài tập đã nộp');
    }

    public function test_admin_assignments_index_groups_and_filters_by_course(): void
    {
        $lesson = $this->course1->lessons()->create(['title' => 'Bài 1', 'sort_order' => 1, 'is_published' => true]);
        $lesson->assignments()->create(['title' => 'Bài tập thực hành admin', 'type' => 'analysis']);

        $response = $this->actingAs($this->admin)->get(route('admin.assignments.index', ['course_id' => $this->course1->id]));
        $response->assertStatus(200);
        $response->assertSee('Bài tập thực hành admin');
        $response->assertSee('Khóa học Price Action Cơ Bản');
    }

    public function test_admin_submissions_index_filters_by_course(): void
    {
        $lesson1 = $this->course1->lessons()->create(['title' => 'Bài 1 Khóa 1', 'sort_order' => 1, 'is_published' => true]);
        $assign1 = $lesson1->assignments()->create(['title' => 'Bài tập Khóa 1', 'type' => 'analysis']);

        $sub = Submission::create([
            'assignment_id' => $assign1->id,
            'student_id' => $this->student->id,
            'content' => 'Bài làm của học viên',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.submissions.index', ['course_id' => $this->course1->id]));
        $response->assertStatus(200);
        $response->assertSee('Bài tập Khóa 1');
        $response->assertSee($this->student->name);
    }

    public function test_admin_can_duplicate_assignment_to_another_course_with_one_click(): void
    {
        // Assignment in Course 1
        $lesson1 = $this->course1->lessons()->create(['title' => 'Bài 1 Khóa 1', 'sort_order' => 1, 'is_published' => true]);
        $originalAssignment = $lesson1->assignments()->create([
            'title' => 'Đề bài tập Price Action Mẫu',
            'description' => 'Mô tả chi tiết bài tập mẫu',
            'requirements' => '1. Chụp ảnh M15 và H4\n2. Nêu rõ RR tối thiểu 1:2',
            'type' => 'backtest',
            'due_date' => now()->addDays(5),
        ]);

        // Target lesson in Course 2
        $targetLesson = $this->course2->lessons()->create(['title' => 'Bài 2 Khóa 2', 'sort_order' => 2, 'is_published' => true]);

        // 1-Click duplicate request
        $response = $this->actingAs($this->admin)->post(route('admin.assignments.duplicate', $originalAssignment->id), [
            'lesson_id' => $targetLesson->id,
            'title' => 'Đề bài tập Price Action Mẫu (Khóa mới)',
            'due_date' => now()->addDays(10)->format('Y-m-d H:i:s'),
        ]);

        $duplicated = Assignment::where('lesson_id', $targetLesson->id)->first();
        $this->assertNotNull($duplicated);

        $response->assertRedirect(route('admin.assignments.show', $duplicated->id));
        $response->assertSessionHas('success');

        // Verify content was cloned accurately
        $this->assertEquals('Đề bài tập Price Action Mẫu (Khóa mới)', $duplicated->title);
        $this->assertEquals($originalAssignment->description, $duplicated->description);
        $this->assertEquals($originalAssignment->requirements, $duplicated->requirements);
        $this->assertEquals($originalAssignment->type, $duplicated->type);
        $this->assertEquals($targetLesson->id, $duplicated->lesson_id);
    }

    public function test_admin_create_page_contains_template_prefill_list(): void
    {
        $lesson = $this->course1->lessons()->create(['title' => 'Bài 1 Khóa 1', 'sort_order' => 1, 'is_published' => true]);
        $assignment = $lesson->assignments()->create([
            'title' => 'Đề bài mẫu kiểm tra',
            'type' => 'analysis',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.assignments.create'));
        $response->assertStatus(200);
        $response->assertSee('Tái sử dụng nhanh từ đề bài cũ');
        $response->assertSee('Đề bài mẫu kiểm tra');
    }
}
