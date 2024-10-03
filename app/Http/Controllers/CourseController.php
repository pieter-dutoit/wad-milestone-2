<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Role
        $isTeacher = Auth::user()->role->role == 'teacher';
        // Find course, teacher, workshop.
        $course = Course::find($id);
        $teachers = $course->users->where('role.role', 'teacher')->unique();
        $workshops = $course->workshops->unique();

        // List of staff, and the workshops they teach.
        $staff = [];

        foreach ($teachers as $teacher) {
            $teacherWorkshops = [];
            foreach ($workshops as $workshop) {
                if ($workshop->pivot->user_id == $teacher->id) {
                    $teacherWorkshops[] = $workshop;
                }
            }
            $staff[] = [
                'teacher' => $teacher,
                'workshops' => $teacherWorkshops
            ];
        }

        $assessments = $isTeacher
            ? $course->assessments
            : $course->submissions->where('student_id', Auth::user()->id);

        return view('courses.show')
            ->with('course', $course)
            ->with('assessments', $assessments)
            ->with('teachers', $staff)
            ->with('isTeacher', $isTeacher);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
