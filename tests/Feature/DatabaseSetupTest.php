<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\ModelTrade;
use App\Models\Notification;
use App\Models\Submission;
use App\Models\SubmissionReview;
use App\Models\TradeEntry;
use App\Models\User;
use App\Models\UserNotification;
use Database\Seeders\AdminSeeder;
use Database\Seeders\SampleDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSetupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
        $this->seed(SampleDataSeeder::class);
    }

    public function test_admin_and_students_exist(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isStudent());

        $student = User::where('role', 'student')->first();
        $this->assertNotNull($student);
        $this->assertTrue($student->isStudent());
        $this->assertFalse($student->isAdmin());
    }

    public function test_courses_and_lessons_relationship(): void
    {
        $course = Course::published()->first();
        $this->assertNotNull($course);
        $this->assertGreaterThan(0, $course->lessons()->count());

        $lesson = $course->lessons()->first();
        $this->assertInstanceOf(Lesson::class, $lesson);
        $this->assertEquals($course->id, $lesson->course->id);

        $assignments = $course->assignments;
        $this->assertNotNull($assignments);
    }

    public function test_assignments_and_submissions(): void
    {
        $assignment = Assignment::first();
        $this->assertNotNull($assignment);
        $this->assertNotNull($assignment->course());
        $this->assertNotNull($assignment->lesson);

        $submission = Submission::first();
        $this->assertNotNull($submission);
        $this->assertInstanceOf(Assignment::class, $submission->assignment);
        $this->assertInstanceOf(User::class, $submission->student);
    }

    public function test_submission_reviews(): void
    {
        $review = SubmissionReview::first();
        $this->assertNotNull($review);
        $this->assertInstanceOf(Submission::class, $review->submission);
        $this->assertInstanceOf(User::class, $review->reviewer);
        $this->assertTrue($review->reviewer->isAdmin());
    }

    public function test_trade_entries_and_scopes(): void
    {
        $this->assertGreaterThan(0, TradeEntry::count());
        $this->assertGreaterThan(0, TradeEntry::wins()->count());
        $this->assertGreaterThan(0, TradeEntry::losses()->count());
        $this->assertGreaterThan(0, TradeEntry::liveTrades()->count());
        $this->assertGreaterThan(0, TradeEntry::demoTrades()->count());
        $this->assertGreaterThan(0, TradeEntry::backtests()->count());
    }

    public function test_model_trades(): void
    {
        $modelTrade = ModelTrade::published()->first();
        $this->assertNotNull($modelTrade);
        $this->assertInstanceOf(User::class, $modelTrade->admin);
        $this->assertTrue($modelTrade->admin->isAdmin());
    }

    public function test_user_notifications(): void
    {
        $this->assertGreaterThan(0, UserNotification::count());
        $this->assertGreaterThan(0, UserNotification::unread()->count());
        $this->assertGreaterThan(0, UserNotification::byType('review')->count());
        $this->assertEquals(UserNotification::count(), Notification::count());
    }

    public function test_admin_middleware(): void
    {
        $admin = User::where('role', 'admin')->first();
        $student = User::where('role', 'student')->first();

        // Register temporary test route with admin middleware
        \Illuminate\Support\Facades\Route::get('/test-admin-route', fn () => response('Admin Success'))
            ->middleware('admin');

        $this->actingAs($admin)->get('/test-admin-route')->assertOk()->assertSee('Admin Success');
        $this->actingAs($student)->get('/test-admin-route')->assertForbidden();
    }

    public function test_student_middleware(): void
    {
        $admin = User::where('role', 'admin')->first();
        $student = User::where('role', 'student')->first();

        // Register temporary test route with student middleware
        \Illuminate\Support\Facades\Route::get('/test-student-route', fn () => response('Student Success'))
            ->middleware('student');

        $this->actingAs($student)->get('/test-student-route')->assertOk()->assertSee('Student Success');
        $this->actingAs($admin)->get('/test-student-route')->assertRedirect();
    }
}
