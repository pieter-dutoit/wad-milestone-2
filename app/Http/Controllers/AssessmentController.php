<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Review;
use App\Models\ReviewType;
use App\Models\Submission;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courseID = $_REQUEST['course'];
        $types = ReviewType::all();
        $course = Course::find($courseID);

        if ($course == null) {
            session()->flash('warning', 'The selected course does not exist.');
            return redirect(route('enrolments.index'));
        }

        return view('assessments.create_form')
            ->with('course', $course)
            ->with('reviewTypes', $types);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:4|max:255',
            'instruction' => 'required|min:4|max:2000',
            'due_date' => 'required|date|after:tomorrow',
            'num_reviews' => 'required|numeric|min:1|max:1000',
            'max_score' => 'required|numeric||min:1|max:100',
            'course_id' => 'required|exists:courses,id',
            'type_id' => 'required|exists:review_types,id'
        ]);

        $assessment = Assessment::create([
            'title' => $request->title,
            'instruction' => $request->instruction,
            'due_date' => $request->due_date,
            'num_reviews' => $request->num_reviews,
            'max_score' => $request->max_score,
            'course_id' => $request->course_id,
            'type_id' => $request->type_id
        ]);

        $assessment->save();

        // Find students enrolled in this course.
        $enrolments = $assessment->course->enrolments->where('user.role.role', 'student');

        // Create a submission for each enrolment/student
        $submissions = [];
        foreach ($enrolments as $enrolment) {
            $submissions[] = [
                'assessment_id' => $assessment->id,
                'student_id' => $enrolment->user_id
            ];
        }
        Submission::insert($submissions);

        if ($assessment->type->type == 'student_select') {
            // Create specified number of (blank) reviews
            foreach ($assessment->submissions as $submission) {
                // Create reviews without reviewees
                for ($i = 0; $i < $assessment->num_reviews; $i++) {
                    Review::create([
                        'reviewee_id' => null,
                        'submission_id' => $submission->id
                    ]);
                }
            }
        };


        if ($assessment->type->type == 'teacher_assign') {
            // Group enrolments by workshop, then by student groups.
            $allGroups = [];
            // Groups enrolments based on workshops
            $enrolmentsByWorkshop = [];
            foreach ($enrolments as $enrolment) {
                $enrolmentsByWorkshop[$enrolment->workshop_id][] = $enrolment;
            }

            // Break students in workshops into groups
            foreach ($enrolmentsByWorkshop as $workshopEnrolments) {
                $workshopGroups = [];
                shuffle($workshopEnrolments);
                $groupSize = $assessment->num_reviews + 1; // Reviewees + 1 reviewer
                $numGroups = intVal(floor(count($workshopEnrolments) / $groupSize));

                // Create at least one group. Floored value may be 0.
                for ($i = 0; $i < ($numGroups > 0 ? $numGroups : 1); $i++) {
                    $workshopGroups[] = [];
                }

                foreach ($workshopEnrolments as $index => $student) {
                    $groupIndex = $index % count($workshopGroups);
                    $workshopGroups[$groupIndex][] = $student;
                }

                $allGroups = array_merge($allGroups, $workshopGroups);
            }

            // Create review based on groups
            foreach ($allGroups as $group) {
                foreach ($group as $reviewer) {
                    $submissionID = Submission::where('student_id', $reviewer->user_id)
                        ->where('assessment_id', $assessment->id)
                        ->get()
                        ->first()
                        ->id;
                    foreach ($group as $reviewee) {
                        if ($reviewee->id != $submissionID) {
                            Review::create([
                                'reviewee_id' => $reviewee->user_id,
                                'submission_id' => $submissionID
                            ]);
                        }
                    }
                }
            }
        }

        session()->flash('success', 'The assessment has been created successfully.');
        return redirect("assessments/$assessment->id");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assessment = Assessment::find($id);
        return view('assessments.show')->with('assessment', $assessment);
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
