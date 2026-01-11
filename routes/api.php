<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ClassModelController;
use App\Http\Controllers\CountyController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoveredLessonController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentPerformanceController;
use App\Http\Controllers\StudentResponseController;

Route::get('/counties', [CountyController::class, 'index']);
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::apiResource('schools', SchoolController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('admins', AdminController::class);
    Route::get('teachers/statistics/summary', [TeacherController::class, 'statistics']);
    Route::apiResource('classes', ClassModelController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('lessons', LessonController::class);
    //Route::apiResource('users', UsersController::class)->only(['store', 'update', 'destroy']);
    Route::post('users', [UsersController::class, 'store']);
    Route::put('users/{user}', [UsersController::class, 'update']);
    Route::delete('users/{user}', [UsersController::class, 'destroy']);
    Route::patch('users/{id}/status', [UsersController::class, 'updateStatus']);
    Route::get('students/statistics/summary', [StudentController::class, 'statistics']);
    Route::post('classes/{id}/assign-courses', [ClassModelController::class, 'assignCourses']);
    Route::get('/teachers/available-managers', [TeacherController::class, 'availableManagers']);
});
Route::middleware('auth:api')->group(function () {

    Route::prefix('students/{student}')->group(function () {
        Route::get('/covered-lessons', [CoveredLessonController::class, 'index']);
        Route::post('/covered-lessons/start', [CoveredLessonController::class, 'startLesson']);
        Route::post('/covered-lessons/{lesson}/complete', [CoveredLessonController::class, 'completeLesson']);
        Route::get('/last-lesson', [CoveredLessonController::class, 'lastLesson']);
        Route::get('/recent-lesson', [CoveredLessonController::class, 'recentLesson']);
        Route::get('/course-recent-lessons/{course}', [CoveredLessonController::class, 'courseRecentLessons']);
        Route::get('/overall-progress', [CoveredLessonController::class, 'overallProgress']);
    });

    Route::get('courses/{course}/progress/{student}', [CoveredLessonController::class, 'progress']);

    Route::prefix('my')->group(function () {
        Route::get('progress', [CoveredLessonController::class, 'overallProgress']);
        Route::get('progress/{course}/{student}', [CoveredLessonController::class, 'progress']);
        Route::get('recent-lessons', [CoveredLessonController::class, 'recentLessons']);
    });
    Route::prefix('subscription')->group(function () {
        Route::get('/plans', [SubscriptionController::class, 'getPlans']);
        Route::post('/initiate-payment', [SubscriptionController::class, 'initiatePayment']);
        Route::get('/check-status/{checkoutRequestId}', [SubscriptionController::class, 'checkPaymentStatus']);
        Route::get('/details', [SubscriptionController::class, 'getSubscriptionDetails']);
        Route::post('/cancel', [SubscriptionController::class, 'cancelSubscription']);
    });
    Route::apiResource('classes', ClassModelController::class)->only(['index', 'show']);
    Route::apiResource('courses', CourseController::class)->only(['index', 'show']);
    Route::apiResource('lessons', LessonController::class)->only(['index', 'show']);
    Route::apiResource('classes', ClassModelController::class)->only(['index', 'show']);
    Route::apiResource('assessments', AssessmentController::class);
    Route::apiResource('questions', QuestionController::class);
    // Route::apiResource('users', UsersController::class)->only(['show', 'index']);
    Route::get('courses/{id}/classes', [CourseController::class, 'classes']);
    Route::get('courses/{id}/assessments', [CourseController::class, 'assessments']);
    Route::get('users', [UsersController::class, 'index']);
    Route::get('users/{user}', [UsersController::class, 'show']);
    Route::get('assessments/course/{course}', [AssessmentController::class, 'byCourse']);
    Route::get('students/{student}/recent-activities', [CoveredLessonController::class, 'recentActivities']);
    Route::get('students/{student}/pending-assessments', [CoveredLessonController::class, 'pendingAssessments']);
    Route::get('courses/{course}/classes', [CourseController::class, 'getClasses']);
    Route::get('lessons/course/{course}', [LessonController::class, 'byCourse']);
    Route::get('lessons/course/{course}/class/{class}', [LessonController::class, 'byCourseAndClass']);
    Route::get('courses/{id}/lessons', [LessonController::class, 'byCourse']);
    Route::get('classes/{id}/courses', [ClassModelController::class, 'courses']);
    Route::get('classes/{id}/lessons', [ClassModelController::class, 'getLessons']);
    Route::get('students/performance', [StudentPerformanceController::class, 'summary']);
    Route::post('assessments/{assessmentId}/submit', [StudentResponseController::class, 'submit']);
    Route::get('classes/{id}/leaderboard', [LeaderboardController::class, 'classLeaderboard']);
    Route::get('courses/{id}/leaderboard', [LeaderboardController::class, 'courseLeaderboard']);
    Route::get('assessments/{assessment}/results', [StudentResponseController::class, 'results']);
    Route::get('lessons/{lesson}/assessment', [AssessmentController::class, 'getByLesson']);
    Route::post('assessments/{assessment}/attempt/start', [StudentResponseController::class, 'startAttempt']);
    Route::post('assessments/{assessment}/submit', [StudentResponseController::class, 'submit']);
    Route::get('assessments/{assessment}/attempts', [StudentResponseController::class, 'getAttempts']);
    Route::get('assessments/{assessment}/attempts/{attempt}/results', [StudentResponseController::class, 'results']);
});
Route::post('/mpesa/callback', [SubscriptionController::class, 'mpesaCallback']);
Route::get('/available-classes', [ClassModelController::class, 'listClasses']);