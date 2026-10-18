<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonQuestion;
use App\Models\QuestionAnswer;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FeaturedAndQuestionSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student1;
    protected User $student2;
    protected Course $course1;
    protected Course $course2;
    protected Lesson $lesson1;
    protected Lesson $lesson2;
    protected Assignment $assignment1;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create([
            'name' => 'Admin Minh',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->student1 = User::factory()->create([
            'name' => 'Nguyễn Văn An',
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->student2 = User::factory()->create([
            'name' => 'Trần Thị Bình',
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->course1 = Course::create([
            'title' => 'Khóa Học Price Action',
            'description' => 'Khóa cơ bản',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $this->course2 = Course::create([
            'title' => 'Khóa Học SMC Chuyên Sâu',
            'description' => 'Khóa nâng cao',
            'is_published' => true,
            'sort_order' => 2,
        ]);

        // Student 1 only enrolled in Course 1
        $this->course1->students()->attach($this->student1->id, ['is_active' => true, 'enrolled_at' => now()]);

        // Student 2 enrolled in Course 2
        $this->course2->students()->attach($this->student2->id, ['is_active' => true, 'enrolled_at' => now()]);

        $this->lesson1 = Lesson::create([
            'course_id' => $this->course1->id,
            'title' => 'Bài 1: Cấu trúc sóng H4',
            'content' => 'Nội dung bài 1...',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->lesson2 = Lesson::create([
            'course_id' => $this->course2->id,
            'title' => 'Bài 1: Order Block và FVG',
            'content' => 'Nội dung bài 2...',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->assignment1 = Assignment::create([
            'lesson_id' => $this->lesson1->id,
            'title' => 'Bài tập thực hành vẽ sóng',
            'description' => 'Vẽ sóng trên cặp EURUSD',
            'type' => 'analysis',
            'points' => 10,
        ]);
    }

    public function test_student_can_ask_question_with_image_in_enrolled_course(): void
    {
        $file = UploadedFile::fake()->image('chart_test.png', 800, 600);

        $response = $this->actingAs($this->student1)->post(
            route('student.lessons.questions.store', $this->lesson1->id),
            [
                'title' => 'Thắc mắc về cấu trúc đỉnh đáy',
                'content' => 'Làm sao để biết nến nào là đỉnh swing high chuẩn ạ?',
                'images' => [$file],
            ]
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('lesson_questions', [
            'lesson_id' => $this->lesson1->id,
            'user_id' => $this->student1->id,
            'title' => 'Thắc mắc về cấu trúc đỉnh đáy',
            'is_featured' => false,
        ]);

        $question = LessonQuestion::where('title', 'Thắc mắc về cấu trúc đỉnh đáy')->first();
        $this->assertNotNull($question);
        $this->assertIsArray($question->images);
        $this->assertCount(1, $question->images);
    }

    public function test_student_cannot_ask_question_in_unenrolled_course(): void
    {
        $response = $this->actingAs($this->student1)->post(
            route('student.lessons.questions.store', $this->lesson2->id),
            [
                'title' => 'Câu hỏi trái phép',
                'content' => 'Em không đăng ký khóa này nhưng cố tình gửi câu hỏi',
            ]
        );

        $response->assertStatus(403);
        $this->assertDatabaseMissing('lesson_questions', [
            'title' => 'Câu hỏi trái phép',
        ]);
    }

    public function test_student_can_view_question_thread_and_reply(): void
    {
        $question = LessonQuestion::create([
            'user_id' => $this->student1->id,
            'lesson_id' => $this->lesson1->id,
            'title' => 'Câu hỏi mẫu',
            'content' => 'Nội dung thắc mắc',
        ]);

        $response = $this->actingAs($this->student1)->get(route('student.questions.show', $question->id));
        $response->assertStatus(200);
        $response->assertSee('Câu hỏi mẫu');

        // Reply to question
        $replyResponse = $this->actingAs($this->student1)->post(
            route('student.questions.reply', $question->id),
            [
                'content' => 'Em bổ sung thêm thông tin là khung M15 ạ.',
            ]
        );

        $replyResponse->assertSessionHas('success');
        $this->assertDatabaseHas('question_answers', [
            'question_id' => $question->id,
            'user_id' => $this->student1->id,
            'content' => 'Em bổ sung thêm thông tin là khung M15 ạ.',
        ]);
    }

    public function test_admin_can_list_and_filter_questions(): void
    {
        LessonQuestion::create([
            'user_id' => $this->student1->id,
            'lesson_id' => $this->lesson1->id,
            'title' => 'Câu hỏi 1 cần lọc',
            'content' => 'Nội dung 1',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.questions.index'));
        $response->assertStatus(200);
        $response->assertSee('Câu hỏi 1 cần lọc');
    }

    public function test_admin_can_answer_question_and_mark_as_featured(): void
    {
        $question = LessonQuestion::create([
            'user_id' => $this->student1->id,
            'lesson_id' => $this->lesson1->id,
            'title' => 'Câu hỏi hay về FVG',
            'content' => 'Hỏi về mức 50% FVG',
        ]);

        $chartReply = UploadedFile::fake()->image('instructor_guide.png', 800, 600);

        $response = $this->actingAs($this->admin)->post(
            route('admin.questions.answer', $question->id),
            [
                'content' => 'Chào em, mức 50% FVG gọi là Consequent Encroachment (C.E)...',
                'images' => [$chartReply],
                'is_featured' => 1,
                'featured_note' => 'Câu hỏi trọng tâm về FVG và cách quản lý rủi ro.',
            ]
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('question_answers', [
            'question_id' => $question->id,
            'user_id' => $this->admin->id,
        ]);

        $question->refresh();
        $this->assertTrue($question->is_featured);
        $this->assertEquals('Câu hỏi trọng tâm về FVG và cách quản lý rủi ro.', $question->featured_note);
        $this->assertNotNull($question->featured_at);
    }

    public function test_admin_can_toggle_featured_status_of_question(): void
    {
        $question = LessonQuestion::create([
            'user_id' => $this->student1->id,
            'lesson_id' => $this->lesson1->id,
            'title' => 'Câu hỏi toggle',
            'content' => 'Nội dung',
            'is_featured' => false,
        ]);

        // Toggle ON
        $response = $this->actingAs($this->admin)->post(
            route('admin.questions.toggle-featured', $question->id),
            ['featured_note' => 'Ghi chú hay']
        );
        $response->assertSessionHas('success');
        $this->assertTrue($question->fresh()->is_featured);

        // Toggle OFF
        $response = $this->actingAs($this->admin)->post(route('admin.questions.toggle-featured', $question->id));
        $response->assertSessionHas('success');
        $this->assertFalse($question->fresh()->is_featured);
    }

    public function test_admin_can_mark_submission_as_featured_in_review(): void
    {
        $submission = Submission::create([
            'assignment_id' => $this->assignment1->id,
            'student_id' => $this->student1->id,
            'content' => 'Bài làm rất kỹ lưỡng của học viên',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(
            route('admin.submissions.storeReview', $submission->id),
            [
                'score' => 9.5,
                'status' => 'approved',
                'feedback' => 'Bài làm xuất sắc, phân tích chuẩn xác!',
                'corrections' => 'Tiếp tục phát huy',
                'is_featured' => 1,
                'featured_note' => 'Bài mẫu thực hành SMC chuẩn 10/10 của lớp.',
            ]
        );

        $response->assertSessionHas('success');

        $submission->refresh();
        $this->assertTrue($submission->is_featured);
        $this->assertEquals('Bài mẫu thực hành SMC chuẩn 10/10 của lớp.', $submission->featured_note);
        $this->assertNotNull($submission->featured_at);
        $this->assertEquals(9.5, $submission->score);
    }

    public function test_admin_can_toggle_featured_status_of_submission(): void
    {
        $submission = Submission::create([
            'assignment_id' => $this->assignment1->id,
            'student_id' => $this->student1->id,
            'content' => 'Bài làm của học viên',
            'status' => 'approved',
            'is_featured' => false,
        ]);

        $response = $this->actingAs($this->admin)->post(
            route('admin.submissions.toggle-featured', $submission->id),
            ['featured_note' => 'Bài tập tiêu biểu']
        );
        $response->assertSessionHas('success');
        $this->assertTrue($submission->fresh()->is_featured);
    }

    public function test_showcase_page_renders_featured_submissions_and_questions(): void
    {
        // Create featured submission in Course 1
        $submission = Submission::create([
            'assignment_id' => $this->assignment1->id,
            'student_id' => $this->student1->id,
            'content' => 'Bài tập tiêu biểu độc nhất',
            'status' => 'approved',
            'score' => 10,
            'is_featured' => true,
            'featured_note' => 'Ghi chú bài mẫu',
            'featured_at' => now(),
        ]);

        // Create featured question in Course 1
        $question = LessonQuestion::create([
            'user_id' => $this->student1->id,
            'lesson_id' => $this->lesson1->id,
            'title' => 'Câu hỏi tiêu biểu độc nhất',
            'content' => 'Nội dung câu hỏi tiêu biểu',
            'is_featured' => true,
            'featured_note' => 'Ghi chú câu hỏi mẫu',
            'featured_at' => now(),
        ]);

        // Student 1 (enrolled in Course 1) views showcase
        $responseSubmissions = $this->actingAs($this->student1)->get(route('student.featured.index', ['tab' => 'submissions']));
        $responseSubmissions->assertStatus(200);
        $responseSubmissions->assertSee('Bài tập tiêu biểu độc nhất');

        $responseQuestions = $this->actingAs($this->student1)->get(route('student.featured.index', ['tab' => 'questions']));
        $responseQuestions->assertStatus(200);
        $responseQuestions->assertSee('Câu hỏi tiêu biểu độc nhất');

        // Student 2 (enrolled in Course 2, NOT Course 1) should NOT see Course 1 featured items
        $responseStudent2 = $this->actingAs($this->student2)->get(route('student.featured.index', ['tab' => 'submissions']));
        $responseStudent2->assertStatus(200);
        $responseStudent2->assertDontSee('Bài tập tiêu biểu độc nhất');

        // Admin sees all in admin showcase
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.featured.index', ['tab' => 'submissions']));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Bài tập tiêu biểu độc nhất');
    }
}

