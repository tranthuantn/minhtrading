<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\TuitionPayment;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TuitionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create([
            'name' => 'Admin Minh',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'name' => 'Nguyễn Văn An',
            'role' => 'student',
            'is_active' => true,
            'billing_type' => 'monthly',
            'monthly_tuition_fee' => 2000000,
            'paid_until' => now()->subDays(5)->toDateString(), // Currently overdue 5 days
        ]);

        $this->course = Course::create([
            'title' => 'Khóa Học Price Action SMC',
            'description' => 'Mô tả',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $this->student->courses()->attach($this->course->id, ['is_active' => true]);
    }

    public function test_admin_can_view_tuition_index_and_filters(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.tuition.index'));
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Học Phí');
        $response->assertSee('Nguyễn Văn An');
        $response->assertSee('Chưa nộp tháng này');

        // Filter by overdue
        $resOverdue = $this->actingAs($this->admin)->get(route('admin.tuition.index', ['status' => 'overdue']));
        $resOverdue->assertStatus(200);
        $resOverdue->assertSee('Nguyễn Văn An');

        // Filter by active (student is overdue so should not see)
        $resActive = $this->actingAs($this->admin)->get(route('admin.tuition.index', ['status' => 'active']));
        $resActive->assertStatus(200);
        $resActive->assertDontSee('Nguyễn Văn An');
    }

    public function test_admin_can_record_monthly_tuition_payment(): void
    {
        $file = UploadedFile::fake()->image('bill.jpg');

        $startDate = now()->startOfDay()->toDateString();
        $endDate = now()->startOfDay()->addMonth()->subDay()->toDateString();

        $response = $this->actingAs($this->admin)->post(route('admin.tuition.store'), [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'billing_type' => 'monthly',
            'months_count' => 1,
            'amount' => 2000000,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payment_method' => 'bank_transfer',
            'transaction_code' => 'FT261019999',
            'payment_proof' => $file,
            'note' => 'Thu học phí Tháng 11/2026',
        ]);

        $response->assertRedirect(route('admin.tuition.index'));
        $response->assertSessionHas('success');

        // Assert database
        $this->assertDatabaseHas('tuition_payments', [
            'user_id' => $this->student->id,
            'admin_id' => $this->admin->id,
            'amount' => 2000000,
            'billing_type' => 'monthly',
            'transaction_code' => 'FT261019999',
        ]);

        $payment = TuitionPayment::where('transaction_code', 'FT261019999')->first();
        $this->assertNotNull($payment);
        $this->assertEquals($endDate, $payment->end_date->format('Y-m-d'));

        // Student's paid_until should be updated
        $this->student->refresh();
        $this->assertEquals($endDate, $this->student->paid_until->toDateString());

        // Assert notification sent to student
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $this->student->id,
            'title' => 'Xác nhận thu học phí thành công',
        ]);
    }

    public function test_admin_can_record_full_course_tuition_payment(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.tuition.store'), [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'billing_type' => 'full_course',
            'months_count' => 1,
            'amount' => 15000000,
            'payment_method' => 'cash',
            'note' => 'Đóng trọn gói cả khóa',
        ]);

        $response->assertRedirect(route('admin.tuition.index'));

        $this->student->refresh();
        $this->assertEquals('full_course', $this->student->billing_type);
        $this->assertNull($this->student->paid_until);

        $status = $this->student->tuition_status;
        $this->assertEquals('full_course', $status['code']);
        $this->assertFalse($status['is_overdue']);
    }

    public function test_admin_can_send_tuition_reminder_1click(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.tuition.remind', $this->student->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $this->student->id,
            'title' => 'Nhắc nhở nộp học phí khóa học',
            'type' => 'announcement',
        ]);
    }

    public function test_student_can_view_own_tuition_page_and_receipts(): void
    {
        // Create sample payment
        TuitionPayment::create([
            'user_id' => $this->student->id,
            'admin_id' => $this->admin->id,
            'amount' => 2000000,
            'billing_type' => 'monthly',
            'months_count' => 1,
            'start_date' => now()->startOfDay()->toDateString(),
            'end_date' => now()->startOfDay()->addMonth()->toDateString(),
            'payment_method' => 'bank_transfer',
            'note' => 'Học phí định kỳ',
        ]);

        $response = $this->actingAs($this->student)->get(route('student.tuition.index'));
        $response->assertStatus(200);
        $response->assertSee('Thông Tin Học Phí');
        $response->assertSee('2.000.000đ');
        $response->assertSee('Techcombank');
        $response->assertSee('Chuyển khoản ngân hàng');
    }

    public function test_admin_can_view_tuition_history_and_student_history(): void
    {
        TuitionPayment::create([
            'user_id' => $this->student->id,
            'admin_id' => $this->admin->id,
            'amount' => 2000000,
            'billing_type' => 'monthly',
            'months_count' => 1,
            'payment_method' => 'bank_transfer',
            'transaction_code' => 'TX12345',
        ]);

        $responseHistory = $this->actingAs($this->admin)->get(route('admin.tuition.history'));
        $responseHistory->assertStatus(200);
        $responseHistory->assertSee('Lịch Sử Giao Dịch Thu Học Phí');
        $responseHistory->assertSee('TX12345');

        $responseStudentHistory = $this->actingAs($this->admin)->get(route('admin.tuition.student', $this->student->id));
        $responseStudentHistory->assertStatus(200);
        $responseStudentHistory->assertSee('Lịch Sử Đóng Tiền: Nguyễn Văn An');
        $responseStudentHistory->assertSee('TX12345');
    }
}
