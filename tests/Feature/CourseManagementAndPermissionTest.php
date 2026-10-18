<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseManagementAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $studentA;
    protected User $studentB;
    protected Course $course1;
    protected Course $course2;
    protected Lesson $lesson1A;
    protected Lesson $lesson1B;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->studentA = User::factory()->create([
            'name' => 'Học viên A',
            'email' => 'student.a@test.com',
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->studentB = User::factory()->create([
            'name' => 'Học viên B',
            'email' => 'student.b@test.com',
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
            'title' => 'Khóa học SMC Nâng Cao',
            'description' => 'Mô tả khóa 2',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $this->lesson1A = $this->course1->lessons()->create([
            'title' => 'Bài 1: Cấu trúc sóng đỉnh đáy',
            'content' => '<h1>Nội dung bài 1</h1><p>Giải thích đỉnh đáy</p>',
            'video_url' => 'https://www.youtube.com/watch?v=sample1',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->lesson1B = $this->course1->lessons()->create([
            'title' => 'Bài 2: Vùng Supply Demand',
            'content' => '<h2>Nội dung bài 2</h2><p>Các vùng cản</p>',
            'video_url' => 'https://www.youtube.com/watch?v=sample2',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        // Cấp quyền: Student A được học Khóa 1, Student B được học Khóa 2
        $this->course1->students()->attach($this->studentA->id, [
            'is_active' => true,
            'enrolled_at' => now(),
        ]);

        $this->course2->students()->attach($this->studentB->id, [
            'is_active' => true,
            'enrolled_at' => now(),
        ]);
    }

    public function test_admin_can_view_courses_list_and_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.courses.index'));
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Khóa Học Forex');
        $response->assertSee('Khóa học Price Action Cơ Bản');

        $createResponse = $this->actingAs($this->admin)->get(route('admin.courses.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Tạo Khóa Học Mới');
    }

    public function test_admin_can_store_course_with_enrolled_students(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.courses.store'), [
            'title' => 'Khóa học Quản Lý Vốn Thực Chiến',
            'description' => 'Mô tả chi tiết quản trị rủi ro',
            'sort_order' => 3,
            'is_published' => 1,
            'student_ids' => [$this->studentA->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'title' => 'Khóa học Quản Lý Vốn Thực Chiến',
            'sort_order' => 3,
        ]);

        $newCourse = Course::where('title', 'Khóa học Quản Lý Vốn Thực Chiến')->first();
        $this->assertTrue($this->studentA->hasCourseAccess($newCourse));
        $this->assertFalse($this->studentB->hasCourseAccess($newCourse));
    }

    public function test_admin_can_store_lesson_with_content_video_attachments_and_sort_order(): void
    {
        $file = UploadedFile::fake()->create('giao_trinh.pdf', 100);

        $response = $this->actingAs($this->admin)->post(route('admin.courses.lessons.store', $this->course1->id), [
            'course_id' => $this->course1->id,
            'title' => 'Bài 3: Cách bắt bẫy thanh khoản Liquidity Sweep',
            'content' => '<h3>Nội dung bài 3</h3><p>Đặc điểm nến rút chân</p>',
            'video_url' => 'https://www.youtube.com/watch?v=sweep123',
            'attachment' => $file,
            'sort_order' => 3,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.courses.show', $this->course1->id));
        $this->assertDatabaseHas('lessons', [
            'course_id' => $this->course1->id,
            'title' => 'Bài 3: Cách bắt bẫy thanh khoản Liquidity Sweep',
            'sort_order' => 3,
        ]);

        $createdLesson = Lesson::where('title', 'Bài 3: Cách bắt bẫy thanh khoản Liquidity Sweep')->first();
        $this->assertNotNull($createdLesson->attachments);
        $this->assertEquals('giao_trinh.pdf', $createdLesson->attachments[0]['name']);
    }

    public function test_admin_can_reorder_lessons(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.courses.lessons.reorder', $this->course1->id), [
            'lessons' => [
                ['id' => $this->lesson1A->id, 'sort_order' => 10],
                ['id' => $this->lesson1B->id, 'sort_order' => 5],
            ],
        ]);

        $response->assertRedirect();
        $this->assertEquals(10, $this->lesson1A->fresh()->sort_order);
        $this->assertEquals(5, $this->lesson1B->fresh()->sort_order);
    }

    public function test_admin_can_enroll_and_unenroll_students(): void
    {
        // Ban đầu student B chưa có quyền học course 1
        $this->assertFalse($this->studentB->hasCourseAccess($this->course1));

        // Admin cấp quyền cho student B vào course 1
        $enrollResponse = $this->actingAs($this->admin)->post(route('admin.courses.enroll', $this->course1->id), [
            'user_ids' => [$this->studentB->id],
        ]);

        $enrollResponse->assertRedirect();
        $this->assertTrue($this->studentB->fresh()->hasCourseAccess($this->course1));

        // Admin hủy quyền student B khỏi course 1
        $unenrollResponse = $this->actingAs($this->admin)->delete(route('admin.courses.unenroll', [
            'course' => $this->course1->id,
            'student' => $this->studentB->id,
        ]));

        $unenrollResponse->assertRedirect();
        $this->assertFalse($this->studentB->fresh()->hasCourseAccess($this->course1));
    }

    public function test_student_can_only_view_enrolled_courses(): void
    {
        // Student A chỉ được cấp quyền Course 1
        $responseA = $this->actingAs($this->studentA)->get(route('student.courses.index'));
        $responseA->assertStatus(200);
        $responseA->assertSee($this->course1->title);
        $responseA->assertDontSee($this->course2->title);

        // Student B chỉ được cấp quyền Course 2
        $responseB = $this->actingAs($this->studentB)->get(route('student.courses.index'));
        $responseB->assertStatus(200);
        $responseB->assertSee($this->course2->title);
        $responseB->assertDontSee($this->course1->title);
    }

    public function test_student_is_forbidden_from_viewing_unauthorized_course_and_lessons(): void
    {
        // Student A cố tình truy cập Course 2 (chưa được cấp quyền) -> 403 Forbidden
        $courseResponse = $this->actingAs($this->studentA)->get(route('student.courses.show', $this->course2->id));
        $courseResponse->assertStatus(403);

        // Tạo bài học trong Course 2
        $lesson2 = $this->course2->lessons()->create([
            'title' => 'Bài học bí mật Course 2',
            'content' => 'Nội dung bí mật',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        // Student A cố tình truy cập bài học trong Course 2 -> 403 Forbidden
        $lessonResponse = $this->actingAs($this->studentA)->get(route('student.courses.lesson', [
            'course' => $this->course2->id,
            'lesson' => $lesson2->id,
        ]));
        $lessonResponse->assertStatus(403);
    }

    public function test_student_can_view_authorized_course_and_lessons(): void
    {
        // Student A truy cập Course 1 (đã được cấp quyền) -> 200 OK
        $courseResponse = $this->actingAs($this->studentA)->get(route('student.courses.show', $this->course1->id));
        $courseResponse->assertStatus(200);
        $courseResponse->assertSee($this->course1->title);
        $courseResponse->assertSee($this->lesson1A->title);

        // Student A xem bài học Bài 1 -> 200 OK
        $lessonResponse = $this->actingAs($this->studentA)->get(route('student.courses.lesson', [
            'course' => $this->course1->id,
            'lesson' => $this->lesson1A->id,
        ]));
        $lessonResponse->assertStatus(200);
        $lessonResponse->assertSee($this->lesson1A->title);
        $lessonResponse->assertSee('Giải thích đỉnh đáy');
    }
}

