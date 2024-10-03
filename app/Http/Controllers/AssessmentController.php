<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Review;
use App\Models\ReviewType;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'title' => 'required|min:4|max:20',
            'instruction' => 'required|min:4|max:1000',
            'due_date' => 'required|date|after:tomorrow',
            'num_reviews' => 'required|numeric|min:1|max:1000',
            'max_score' => 'required|numeric|min:1|max:100',
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
        $submissions = Submission::where('assessment_id', $assessment->id);

        if ($assessment->type->type == 'student_select') {
            // Create specified number of (blank) reviews
            foreach ($submissions as $submission) {
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
            foreach ($allGroups as $index => $group) {
                foreach ($group as $reviewer) {
                    $submission = Submission::where('student_id', $reviewer->user_id)
                        ->where('assessment_id', $assessment->id)
                        ->get()
                        ->first();

                    // Assign a group number
                    $submission->update([
                        'group_num' => $index + 1
                    ]);
                    $submission->save();

                    foreach ($group as $reviewee) {
                        if ($reviewee->user_id != $reviewer->user_id) {
                            Review::create([
                                'reviewee_id' => $reviewee->user_id,
                                'submission_id' => $submission->id
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

        $PAGE_SIZE = 10;
        $page = $_REQUEST['page'] ?? 1;
        $offset = ($page - 1) * $PAGE_SIZE;

        $totalStudents = $assessment->submissions->count();
        $numPages = ceil($totalStudents / $PAGE_SIZE);

        $teachers = $assessment->course->users->where('role.role', 'teacher')->unique();
        $submissions = $assessment->submissions()->orderBy('group_num', 'asc')->skip($offset)->take($PAGE_SIZE)->get();
        $course = $assessment->course;

        $userId = Auth::user()->id;

        $canViewPage = false;
        foreach ($teachers as $teacher) {
            if ($teacher->id == $userId) {
                $canViewPage = true;
            }
        }

        if (!$canViewPage) {
            session()->flash('warning', 'You do not have permission to view that page.');
            return redirect(route('enrolments.index'));
        }

        // lock for editing if not yet any
        $submittedReviewCount = $assessment->submissions->where('date_submitted', '!=', null)->count();

        return view('assessments.show')
            ->with('assessment', $assessment)
            ->with('course', $course)
            ->with('submissions', $submissions)
            ->with('submittedReviewCount', $submittedReviewCount)
            ->with('prevPage', $page > 1 ? $page - 1 : 1)
            ->with('nextPage', $page < $numPages ? $page + 1 : $numPages)
            ->with('page', $page)
            ->with('numPages', $numPages);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $assessment = Assessment::find($id);
        $submittedReviewCount = $assessment->submissions->where('date_submitted', '!=', null)->count();

        if ($submittedReviewCount > 0) {
            session()->flash('warning', 'This assessment cannot be edited, because it has one or more submissions.');
            return redirect()->back();
        }

        $types = ReviewType::all();
        $course = $assessment->course;

        if ($course == null) {
            session()->flash('warning', 'The selected course does not exist.');
            return redirect(route('enrolments.index'));
        }

        return view('assessments.edit_form')
            ->with('assessment', $assessment)
            ->with('course', $course)
            ->with('reviewTypes', $types);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $assessment = Assessment::find($id);
        $submittedReviewCount = $assessment->submissions->where('date_submitted', '!=', null)->count();

        if ($submittedReviewCount > 0) {
            session()->flash('warning', 'This assessment cannot be edited, because it has one or more submissions.');
            return redirect()->back();
        }

        $request->validate([
            'title' => 'required|min:4|max:20',
            'instruction' => 'required|min:4|max:1000',
            'due_date' => 'required|date|after:tomorrow',
            'num_reviews' => 'required|numeric|min:1|max:1000',
            'max_score' => 'required|numeric|min:1|max:100',
            'type_id' => 'required|exists:review_types,id'
        ]);

        $assessment->update([
            'title' => $request->title,
            'instruction' => $request->instruction,
            'due_date' => $request->due_date,
            'num_reviews' => $request->num_reviews,
            'max_score' => $request->max_score,
            'type_id' => $request->type_id
        ]);

        $assessment->save();

        // Delete existing submissions and reviews
        Review::whereHas('submission', function ($query) use ($id) {
            $query->where('assessment_id', $id);
        })->delete();
        Submission::where('assessment_id', $id)->delete();


        // Recreate submissions and reviews
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
        $submissions = Submission::where('assessment_id', $id);

        if ($assessment->type->type == 'student_select') {
            // Create specified number of (blank) reviews
            foreach ($submissions as $submission) {
                // Create reviews without reviewees
                for ($i = 0; $i < $assessment->num_reviews; $i++) {
                    echo $submission->id;
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
            foreach ($allGroups as $index => $group) {
                foreach ($group as $reviewer) {
                    $submission = Submission::where('student_id', $reviewer->user_id)
                        ->where('assessment_id', $assessment->id)
                        ->get()
                        ->first();

                    // Assign a group number
                    $submission->update([
                        'group_num' => $index + 1
                    ]);
                    $submission->save();

                    foreach ($group as $reviewee) {
                        if ($reviewee->user_id != $reviewer->user_id) {
                            Review::create([
                                'reviewee_id' => $reviewee->user_id,
                                'submission_id' => $submission->id
                            ]);
                        }
                    }
                }
            }
        }

        session()->flash('success', 'The assessment has been updated successfully.');
        return redirect("assessments/$assessment->id");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
