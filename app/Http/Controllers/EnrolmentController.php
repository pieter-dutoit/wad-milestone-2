<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrolmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $enrolments = $user->enrolments;
        return view('enrolments.index')->with('enrolments', $enrolments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courseID = $_REQUEST['course'];
        $workshops = Workshop::all();
        $course = Course::find($courseID);

        if ($course == null) {
            session()->flash('warning', 'The selected course does not exist.');
            return redirect(route('enrolments.index'));
        }

        $students = User::whereHas('role', function ($query) {
            $query->where('role', 'student');
        })
            ->whereDoesntHave('enrolments', function ($query) use ($courseID) {
                $query->where('course_id', $courseID);
            })
            ->get();

        return view('enrolments.create_form')
            ->with('course', $course)
            ->with('workshops', $workshops)
            ->with('students', $students);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student' => 'required|exists:users,id',
            'workshop' => 'required|exists:workshops,id'
        ]);

        $enrolment = Enrolment::create([
            'user_id' => $request->student,
            'workshop_id' => $request->workshop,
            'course_id' => $request->course_id
        ]);

        $enrolment->save();


        session()->flash('success', 'The enrolment has been created successfully.');
        return redirect("courses/$request->course_id");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
