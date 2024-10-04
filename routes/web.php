<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrolmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(route('enrolments.index'));
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Course Routes
    Route::get('/courses/{id}', [CourseController::class, 'show'])->middleware('role:teacher,student');

    // Enrolment Routes
    Route::get('/enrolments', [EnrolmentController::class, 'index'])
        ->name('enrolments.index')
        ->middleware('role:teacher,student');
    Route::get('/enrolments/create', [EnrolmentController::class, 'create'])->middleware('role:teacher');
    Route::post('/enrolments', [EnrolmentController::class, 'store'])->middleware('role:teacher');

    // Assessment Routes
    Route::get('/assessments', [AssessmentController::class, 'index'])->middleware('role:teacher');
    Route::get('/assessments/create', [AssessmentController::class, 'create'])->middleware('role:teacher');
    Route::post('/assessments', [AssessmentController::class, 'store'])->middleware('role:teacher');
    Route::get('/assessments/{id}', [AssessmentController::class, 'show'])->middleware('role:teacher');
    Route::get('/assessments/{id}/edit', [AssessmentController::class, 'edit'])->middleware('role:teacher');
    Route::put('/assessments/{id}', [AssessmentController::class, 'update'])->middleware('role:teacher');

    // Submission Routes
    Route::get('/submissions/{id}', [SubmissionController::class, 'show'])->middleware('role:student');
    Route::get('/submissions/{id}/edit', [SubmissionController::class, 'edit'])->middleware('role:student,teacher');
    Route::put('/submissions/{id}', [SubmissionController::class, 'update'])->middleware('role:student,teacher');

    // Review Routes
    Route::put('/reviews/{id}', [ReviewController::class, 'report'])->middleware('role:student');

    // Uploads
    Route::get('/uploads/create', [UploadController::class, 'create'])->middleware('role:teacher');
    Route::post('/uploads', [UploadController::class, 'store'])->middleware('role:teacher');
});

require __DIR__ . '/auth.php';
