<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ClassModelController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPerformanceController;
use App\Http\Controllers\StudentResponseController;

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
    Route::apiResource('classes', ClassModelController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('lessons', LessonController::class);
    Route::post('classes/{id}/assign-courses', [ClassModelController::class, 'assignCourses']);
});
Route::middleware('auth:api')->group(function () {
    Route::get('courses/{id}/lessons', [LessonController::class, 'byCourse']);
    Route::get('classes/{id}/courses', [ClassModelController::class, 'courses']);
    Route::apiResource('assessments', AssessmentController::class);
    Route::get('students/performance', [StudentPerformanceController::class, 'summary']);
    Route::apiResource('questions', QuestionController::class);
    Route::post('assessments/{assessmentId}/submit', [StudentResponseController::class, 'submit']);
    Route::get('classes/{id}/leaderboard', [LeaderboardController::class, 'classLeaderboard']);
    Route::get('courses/{id}/leaderboard', [LeaderboardController::class, 'courseLeaderboard']);
    Route::get('assessments/{assessment}/results', [StudentResponseController::class, 'results']);
});
