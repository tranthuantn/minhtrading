<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect after login based on role
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Students management
    Route::resource('students', Admin\StudentController::class);
    Route::get('students/{student}/stats', [Admin\StudentController::class, 'stats'])->name('students.stats');

    // Courses & Permissions
    Route::resource('courses', Admin\CourseController::class);
    Route::post('courses/{course}/toggle', [Admin\CourseController::class, 'toggle'])->name('courses.toggle');
    Route::post('courses/{course}/enroll', [Admin\CourseController::class, 'enrollStudent'])->name('courses.enroll');
    Route::delete('courses/{course}/unenroll/{student}', [Admin\CourseController::class, 'unenrollStudent'])->name('courses.unenroll');

    // Lessons & Reordering & Editor Image Upload
    Route::resource('courses.lessons', Admin\LessonController::class);
    Route::post('courses/{course}/lessons/reorder', [Admin\LessonController::class, 'reorder'])->name('courses.lessons.reorder');
    Route::post('lessons/{lesson}/toggle', [Admin\LessonController::class, 'toggle'])->name('lessons.toggle');
    Route::post('lessons/upload-image', [Admin\LessonController::class, 'uploadImage'])->name('lessons.upload-image');

    // Standalone lesson routes for convenience
    Route::get('lessons/create', [Admin\LessonController::class, 'createStandalone'])->name('lessons.create');
    Route::post('lessons', [Admin\LessonController::class, 'storeStandalone'])->name('lessons.store');
    Route::get('lessons/{lesson}/edit', [Admin\LessonController::class, 'editStandalone'])->name('lessons.edit');
    Route::put('lessons/{lesson}', [Admin\LessonController::class, 'updateStandalone'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [Admin\LessonController::class, 'destroyStandalone'])->name('lessons.destroy');

    // Assignments
    Route::resource('assignments', Admin\AssignmentController::class);
    Route::post('assignments/{assignment}/duplicate', [Admin\AssignmentController::class, 'duplicate'])->name('assignments.duplicate');
    Route::get('assignments-stats', [Admin\AssignmentController::class, 'stats'])->name('assignments.stats');

    // Submissions
    Route::get('submissions', [Admin\SubmissionController::class, 'index'])->name('submissions.index');
    Route::post('submissions/bulk-review', [Admin\SubmissionController::class, 'bulkReview'])->name('submissions.bulk-review');
    Route::get('submissions/{submission}', [Admin\SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('submissions/{submission}/review', [Admin\SubmissionController::class, 'review'])->name('submissions.review');
    Route::post('submissions/{submission}/review', [Admin\SubmissionController::class, 'storeReview'])->name('submissions.storeReview');
    Route::post('submissions/{submission}/toggle-featured', [Admin\SubmissionController::class, 'toggleFeatured'])->name('submissions.toggle-featured');

    // Questions & Answers
    Route::resource('questions', Admin\QuestionController::class)->only(['index', 'show', 'destroy']);
    Route::post('questions/{question}/answer', [Admin\QuestionController::class, 'answer'])->name('questions.answer');
    Route::post('questions/{question}/toggle-featured', [Admin\QuestionController::class, 'toggleFeatured'])->name('questions.toggle-featured');

    // Featured Showcase
    Route::get('featured', [Admin\FeaturedController::class, 'index'])->name('featured.index');

    // Model Trades
    Route::resource('model-trades', Admin\ModelTradeController::class);
    Route::get('model-trades/from-trade/{tradeEntry}', [Admin\ModelTradeController::class, 'createFromTrade'])->name('model-trades.from-trade');
    Route::post('model-trades/from-trade', [Admin\ModelTradeController::class, 'storeFromTrade'])->name('model-trades.store-from-trade');
    Route::post('model-trades/{modelTrade}/toggle', [Admin\ModelTradeController::class, 'toggle'])->name('model-trades.toggle');

    // Trade Statistics
    Route::get('trade-stats', [Admin\TradeStatController::class, 'index'])->name('trade-stats.index');
    Route::get('trade-stats/{student}', [Admin\TradeStatController::class, 'studentStats'])->name('trade-stats.student');

    // Notifications
    Route::resource('notifications', Admin\NotificationController::class)->only(['index', 'create', 'store', 'destroy']);
});

// Student Routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [Student\DashboardController::class, 'index'])->name('dashboard');

    // Courses & Lessons
    Route::get('courses', [Student\CourseController::class, 'index'])->name('courses.index');
    Route::get('courses/{course}', [Student\CourseController::class, 'show'])->name('courses.show');
    Route::get('courses/{course}/lessons/{lesson}', [Student\CourseController::class, 'lesson'])->name('courses.lesson');

    // Questions & Discussions
    Route::post('questions', [Student\QuestionController::class, 'storeGeneral'])->name('questions.store');
    Route::post('lessons/{lesson}/questions', [Student\QuestionController::class, 'store'])->name('lessons.questions.store');
    Route::get('questions/{question}', [Student\QuestionController::class, 'show'])->name('questions.show');
    Route::post('questions/{question}/reply', [Student\QuestionController::class, 'reply'])->name('questions.reply');

    // Featured Showcase
    Route::get('featured', [Student\FeaturedController::class, 'index'])->name('featured.index');

    // Assignments
    Route::get('assignments', [Student\AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('assignments/{assignment}', [Student\AssignmentController::class, 'show'])->name('assignments.show');
    Route::post('assignments/{assignment}/submit', [Student\AssignmentController::class, 'submit'])->name('assignments.submit');
    Route::get('my-submissions', [Student\AssignmentController::class, 'mySubmissions'])->name('submissions.index');
    Route::get('my-submissions/{submission}', [Student\AssignmentController::class, 'showSubmission'])->name('submissions.show');

    // Backtest
    Route::resource('backtests', Student\BacktestController::class);
    Route::get('backtest-stats', [Student\BacktestController::class, 'stats'])->name('backtests.stats');

    // Live/Demo Trades
    Route::resource('trades', Student\TradeController::class);
    Route::get('trade-stats', [Student\TradeController::class, 'stats'])->name('trades.stats');

    // Model Trades
    Route::get('model-trades', [Student\ModelTradeController::class, 'index'])->name('model-trades.index');
    Route::get('model-trades/{modelTrade}', [Student\ModelTradeController::class, 'show'])->name('model-trades.show');

    // Notifications
    Route::get('notifications', [Student\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{notification}', [Student\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('notifications/mark-all-read', [Student\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

require __DIR__.'/auth.php';
