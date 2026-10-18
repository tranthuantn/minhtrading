<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\ModelTrade;
use App\Models\Submission;
use App\Models\TradeEntry;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinhTradingFullFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected Course $course;
    protected Lesson $lesson;
    protected Assignment $assignment;
    protected TradeEntry $tradeEntry;
    protected Submission $submission;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::factory()->create([
            'name' => 'Admin Minh',
            'email' => 'admin@minhtrading.com',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Student
        $this->student = User::factory()->create([
            'name' => 'Nguyen Van An',
            'email' => 'student@minhtrading.com',
            'role' => 'student',
            'is_active' => true,
        ]);

        // Create Course & Lesson
        $this->course = Course::create([
            'title' => 'Price Action Co Ban',
            'description' => 'Khoa hoc Price Action',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $this->lesson = Lesson::create([
            'course_id' => $this->course->id,
            'title' => 'Cau truc thi truong',
            'content' => 'Noi dung bai hoc ve BOS va CHoCH',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        // Cấp quyền học cho student
        $this->course->students()->attach($this->student->id, [
            'is_active' => true,
            'enrolled_at' => now(),
        ]);

        // Create Assignment
        $this->assignment = Assignment::create([
            'lesson_id' => $this->lesson->id,
            'title' => 'Xac dinh BOS tren EURUSD',
            'description' => 'Chup man hinh chart va chi ra BOS',
            'type' => 'analysis',
            'due_date' => now()->addDays(7),
        ]);

        // Create Trade Entry for Student
        $this->tradeEntry = TradeEntry::create([
            'user_id' => $this->student->id,
            'type' => 'live',
            'currency_pair' => 'EUR/USD',
            'direction' => 'buy',
            'entry_price' => 1.08500,
            'stop_loss' => 1.08200,
            'take_profit' => 1.09100,
            'lot_size' => 0.5,
            'result' => 'win',
            'pnl_pips' => 60.0,
            'pnl_amount' => 300.0,
        ]);

        // Create Submission
        $this->submission = Submission::create([
            'assignment_id' => $this->assignment->id,
            'student_id' => $this->student->id,
            'content' => 'Day la bai nop cua em ve BOS tren khung H4',
            'status' => 'pending',
        ]);
    }

    public function test_welcome_page_can_be_rendered(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('MinhTrading');
    }

    public function test_guests_are_redirected_to_login_for_protected_routes(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/student/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_student_cannot_access_admin_portal(): void
    {
        $response = $this->actingAs($this->student)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_dashboard_and_features(): void
    {
        $this->actingAs($this->admin);

        // Dashboard
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Students Management
        $response = $this->get(route('admin.students.index'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.students.show', $this->student));
        $response->assertStatus(200);

        // Courses & Lessons
        $response = $this->get(route('admin.courses.index'));
        $response->assertStatus(200);

        // Assignments
        $response = $this->get(route('admin.assignments.index'));
        $response->assertStatus(200);

        // Submissions & Review
        $response = $this->get(route('admin.submissions.index'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.submissions.review', $this->submission));
        $response->assertStatus(200);

        // Trade Stats
        $response = $this->get(route('admin.trade-stats.index'));
        $response->assertStatus(200);

        // Model Trades
        $response = $this->get(route('admin.model-trades.index'));
        $response->assertStatus(200);
    }

    public function test_student_dashboard_and_features(): void
    {
        $this->actingAs($this->student);

        // Dashboard
        $response = $this->get(route('student.dashboard'));
        $response->assertStatus(200);

        // Courses & Lesson
        $response = $this->get(route('student.courses.index'));
        $response->assertStatus(200);

        $response = $this->get(route('student.courses.lesson', [$this->course, $this->lesson]));
        $response->assertStatus(200);

        // Assignments
        $response = $this->get(route('student.assignments.index'));
        $response->assertStatus(200);

        $response = $this->get(route('student.assignments.show', $this->assignment));
        $response->assertStatus(200);

        // Backtests
        $response = $this->get(route('student.backtests.index'));
        $response->assertStatus(200);

        // Live/Demo Trades
        $response = $this->get(route('student.trades.index'));
        $response->assertStatus(200);

        // Model Trades
        $response = $this->get(route('student.model-trades.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_review_submission(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.submissions.storeReview', $this->submission), [
            'score' => 9.5,
            'feedback' => 'Phan tich rat tot, vung BOS chinh xac',
            'status' => 'approved',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('submission_reviews', [
            'submission_id' => $this->submission->id,
            'reviewer_id' => $this->admin->id,
            'score' => 9.5,
        ]);
        $this->assertDatabaseHas('submissions', [
            'id' => $this->submission->id,
            'status' => 'approved',
        ]);
    }

    public function test_student_can_create_trade_entry(): void
    {
        $this->actingAs($this->student);

        $response = $this->post(route('student.trades.store'), [
            'type' => 'live',
            'currency_pair' => 'GBP/USD',
            'direction' => 'sell',
            'entry_price' => 1.26500,
            'stop_loss' => 1.26800,
            'take_profit' => 1.25800,
            'lot_size' => 0.2,
            'result' => 'win',
            'pnl_pips' => 70,
            'pnl_amount' => 140,
            'analysis_notes' => 'Test trade entry',
        ]);

        $response->assertRedirect(route('student.trades.index'));
        $this->assertDatabaseHas('trade_entries', [
            'user_id' => $this->student->id,
            'currency_pair' => 'GBP/USD',
            'direction' => 'sell',
            'type' => 'live',
        ]);
    }
}

