<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiCustomerController;
use App\Http\Controllers\Api\ApiFinanceController;
use App\Http\Controllers\Api\ApiReminderController;
use App\Http\Controllers\Api\ApiServiceController;
use Illuminate\Support\Facades\Route;

// Public Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/register', [ApiAuthController::class, 'register']);
});

// Protected Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Profile
    Route::get('/auth/me', [ApiAuthController::class, 'me']);

    // User & Staff Management
    Route::get('/users/pending', [ApiAuthController::class, 'usersPending']);
    Route::get('/users/all', [ApiAuthController::class, 'usersAll']);
    Route::put('/users/{id}/approve', [ApiAuthController::class, 'approveUser']);
    Route::put('/users/{id}/reject', [ApiAuthController::class, 'rejectUser']);
    Route::put('/users/{id}/role', [ApiAuthController::class, 'updateRole']);

    // Customers
    Route::get('/customers', [ApiCustomerController::class, 'index']);
    Route::post('/customers', [ApiCustomerController::class, 'store']);
    Route::get('/customers/{id}', [ApiCustomerController::class, 'show']);
    Route::put('/customers/{id}', [ApiCustomerController::class, 'update']);
    Route::delete('/customers/{id}', [ApiCustomerController::class, 'destroy']);

    // Services
    Route::post('/services', [ApiServiceController::class, 'store']);
    Route::get('/services/customer/{customerId}', [ApiServiceController::class, 'customerServices']);

    // Reminders
    Route::get('/reminders', [ApiReminderController::class, 'index']);
    Route::put('/reminders/{id}/mark-sent', [ApiReminderController::class, 'markSent']);

    // Finance Stats
    Route::get('/finance/stats', [ApiFinanceController::class, 'stats']);
});

// ==========================================
// AKADEMİK İŞ İSTASYONU & TEKRAR SİSTEMİ (FAZ 2)
// ==========================================
use App\Http\Controllers\Api\ApiAcademicController;

Route::prefix('academic')->group(function () {
    Route::get('/courses', [ApiAcademicController::class, 'getCourses']);
    Route::delete('/courses/{courseId}', [ApiAcademicController::class, 'deleteCourse']);
    Route::post('/courses/{courseId}/delete', [ApiAcademicController::class, 'deleteCourse']);
    Route::post('/courses/delete-bulk', [ApiAcademicController::class, 'deleteCoursesBulk']);
    Route::post('/courses/{courseId}/rename', [ApiAcademicController::class, 'renameCourse']);

    Route::get('/courses/{courseId}/topics', [ApiAcademicController::class, 'getTopics']);
    Route::delete('/topics/{topicId}', [ApiAcademicController::class, 'deleteTopic']);
    Route::post('/topics/{topicId}/delete', [ApiAcademicController::class, 'deleteTopic']);
    Route::post('/topics/{topicId}/rename', [ApiAcademicController::class, 'renameTopic']);

    Route::get('/topics/{topicId}/cards', [ApiAcademicController::class, 'getCardsInTopic']);
    Route::delete('/cards/{cardId}', [ApiAcademicController::class, 'deleteCard']);
    Route::post('/cards/{cardId}/delete', [ApiAcademicController::class, 'deleteCard']);

    Route::get('/due-cards', [ApiAcademicController::class, 'getDueCards']);
    Route::post('/reviews/submit', [ApiAcademicController::class, 'submitReview']);
    Route::post('/cards/upload-photo', [ApiAcademicController::class, 'uploadPhoto']);
    Route::post('/sync', [ApiAcademicController::class, 'syncBatch']);
});
