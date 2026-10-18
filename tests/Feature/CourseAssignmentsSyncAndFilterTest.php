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

    public function test_creating_a_lesson_automatically_creates_corresponding_assignment(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.courses.lessons.store', $this->course1->id), [
            'title' => 'Bài 1: Xác định cấu trúc thị trường',
            'content' => '<p>Hướng dẫn xác định đỉnh đáy</p>',
            'sort_order' => 1,
            'is_published' => 1,
            'assignment_type' => 'analysis',
            'assignment_requirements' => 'Vẽ 3 con sóng đẩy và 3 con sóng hồi',
        ]);

        $response->assertRedirect(route('admin.courses.show', $this->course1->id));
        $this->assertDatabaseHas('lessons', [
            'course_id' => $this->course1->id,
            'title' => 'Bài 1: Xác định cấu trúc thị trường',
        ]);

        $lesson = Lesson::where('title', 'Bài 1: Xác định cấu trúc thị trường')->first();
        $this->assertNotNull($lesson);

        // Verify assignment was automatically created
        $this->assertDatabaseHas('assignments', [
            'lesson_id' => $lesson->id,
            'title' => 'Bài tập: Bài 1: Xác định cấu trúc thị trường',
            'type' => 'analysis',
            'requirements' => 'Vẽ 3 con sóng đẩy và 3 con sóng hồi',
        ]);
    }

    public function test_updating_a_lesson_syncs_assignment(): void
    {
        $lesson = $this->course1->lessons()->create([
            'title' => 'Bài 2: Vùng Supply Demand',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $lesson->assignments()->create([
            'title' => 'Bài tập: Bài 2: Vùng Supply Demand',
            'description' => 'Mô tả ban đầu',
            'type' => 'analysis',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.courses.lessons.update', [$this->course1->id, $lesson->id]), [
            'title' => 'Bài 2: Các vùng Cung Cầu quan trọng',
            'assignment_title' => 'Bài tập: Các vùng Cung Cầu quan trọng',
            'assignment_type' => 'backtest',
            'sort_order' => 2,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.courses.show', $this->course1->id));

        $this->assertDatabaseHas('assignments', [
            'lesson_id' => $lesson->id,
            'title' => 'Bài tập: Các vùng Cung Cầu quan trọng',
            'type' => 'backtest',
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
}
