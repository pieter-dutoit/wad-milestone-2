<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrolmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\VerifyUserRole;
use App\Models\Course;
use App\Models\Workshop;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    $course = Course::find(2);
    $users = $course->users->where('role.role', 'teacher')->unique('id');
    dd($users);

    $data = [];

    foreach ($users as $user) {
        $workshop = Workshop::find($user->pivot->workshop_id);
        $data[$user->id] = [$user, $workshop];
        // echo ($workshop->location);
    }

    dd($data);
});

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
});

require __DIR__ . '/auth.php';
