<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Review;
use App\Models\ReviewType;
use App\Models\Submission;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
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
        return view('uploads.create_form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|max:10000',
        ]);
        // https://medium.com/@tutsmake.com/laravel-11-file-upload-example-tutorial-a6d3634f49d6
        // https://stackoverflow.com/questions/50122623/parse-json-data-from-file-upload
        $file = $request->file('bulk_file');
        $content = file_get_contents($file);
        $json = json_decode($content, true);

        // Validate file fields
        $fieldValidator = Validator::make($json, [
            'course' => 'required',
            'teachers' => 'required',
            'students' => 'required',
            'assessments' => 'required'
        ]);
        if ($fieldValidator->fails()) {
            return redirect()
                ->back()
                ->withErrors($fieldValidator)
                ->withInput();
        }

        $course = $json['course'];
        $teachers = $json['teachers'];
        $students = $json['students'];
        $assessments = $json['assessments'];


        // Validate course
        $courseValidator = Validator::make($course, [
            'course_code' => 'required|min:4|max:20',
            'course_name' => 'required|min:4|max:20'
        ]);
        if ($courseValidator->fails()) {
            return redirect()
                ->back()
                ->withErrors($courseValidator)
                ->withInput();
        }

        // Validate users
        $users = array_merge($teachers, $students);
        foreach ($users as $user) {
            $userValidation = Validator::make($user, [
                'name' => 'required|min:4|max:20',
                'email' => 'required|email',
                's_number' => 'required|min:4|max:10',
                'workshop' => 'required|min:4|max:50'
            ]);

            if ($userValidation->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($userValidation)
                    ->withInput();
            }
        }

        // Validate Assessments
        foreach ($assessments as $assessment) {
            $assessmentValidation = Validator::make($assessment, [
                'title' => 'required|min:4|max:20',
                'instruction' => 'required|min:4|max:1000',
                'due_date' => 'required|date|after:tomorrow',
                'num_reviews' => 'required|numeric|min:1|max:1000',
                'max_score' => 'required|numeric|min:1|max:100',
                'type' => 'required|in:student_select,teacher_assign'
            ]);
            if ($assessmentValidation->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($assessmentValidation)
                    ->withInput();
            }
        }

        // Save data

        // Find or create the course
        $c = Course::where('course_code', $course['course_code'])->first();
        if ($c == null) {
            $c = Course::create($course);
        }

        // Find or create the assessments
        $a = [];
        foreach ($assessments as $assessment) {
            $existingAssessment = Assessment::where('course_id', $c->id)->where('title', $assessment['title'])->first();

            if ($existingAssessment == null) {
                $assessment['type_id'] = ReviewType::where('type', $assessment['type'])->first()->id;
                unset($assessment['type']);
                $assessment['course_id'] = $c->id;
                $a[] = Assessment::create($assessment);
            } else {
                $a[] = $existingAssessment;
            }
        }

        // Find or create workshops
        $workshops = [];
        $w = [];
        foreach (array_merge($students, $teachers) as $user) {
            $workshops[] = $user['workshop'];
        }
        foreach (array_unique($workshops) as $workshop) {
            $ws = explode(':', $workshop);
            $existingWorkshop = Workshop::where('day', $ws[1])->where('location', $ws[0])->first();

            if ($existingWorkshop == null) {
                $w[] = Workshop::create([
                    'location' => $ws[0],
                    'day' => $ws[1]
                ]);
            } else {
                $w[] = $existingWorkshop;
            }
        }

        // Keep track of student enrolments, for creating submissions and reviews later
        $studentsCreated = [];
        $studentEnrolments = [];
        $toEnrol = [
            1 => $teachers,
            2 => $students
        ];

        foreach ($toEnrol as $roleID => $users) {
            foreach ($users as $user) {
                $ws = explode(':', $user['workshop']);
                $wsId = Workshop::where('day', $ws[1])->where('location', $ws[0])->first()->id;
                unset($user['workshop']);
                $user['role_id'] = $roleID;
                // Find or create user
                $userToEnrol = User::where('s_number', $user['s_number'])->first();
                if ($userToEnrol == null) {
                    $userToEnrol = User::create($user);
                }
                if ($roleID == 2) {
                    $studentsCreated[] = $userToEnrol;
                }

                // Find or Create enrolment
                $enrolment = Enrolment::where('user_id', $userToEnrol->id)->where('course_id', $c->id)->first();
                if ($enrolment == null) {
                    $enrolment =  Enrolment::create([
                        'user_id' => $userToEnrol->id,
                        'course_id' => $c->id,
                        'workshop_id' => $wsId
                    ]);
                }
                if ($roleID == 2) {
                    $studentEnrolments[] = $enrolment;
                }
            }
        }



        // Create a submission for each enrolment/student, for each assessment
        foreach ($a as $assessment) {
            $submissions = [];
            foreach ($studentEnrolments as $enrolment) {
                $existingSub = Submission::where('assessment_id', $assessment->id)->where('student_id', $enrolment->user_id)->first();
                if ($existingSub == null) {
                    $existingSub = Submission::create([
                        'assessment_id' => $assessment->id,
                        'student_id' => $enrolment->user_id
                    ]);
                }
                $submissions[] = $existingSub;
            }

            // Create student-select reviews
            if ($assessment->type->type == 'student_select') {
                // Create specified number of (blank) reviews
                foreach ($submissions as $submission) {
                    // Delete existing reviews to prevent duplicates
                    Review::where('submission_id', $submission->id)->delete();

                    // Create reviews without reviewees
                    for ($i = 0; $i < $assessment->num_reviews; $i++) {
                        Review::create([
                            'reviewee_id' => null,
                            'submission_id' => $submission->id
                        ]);
                    }
                }
            };

            // Create teacher-assign reviews
            if ($assessment->type->type == 'teacher_assign') {
                // Group enrolments by workshop, then by student groups.
                $allGroups = [];
                // Groups enrolments based on workshops
                $enrolmentsByWorkshop = [];
                foreach ($studentEnrolments as $enrolment) {
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

                        // Delete pre-existing peer reviews to prevent duplicates
                        Review::where('submission_id', $submission->id)->delete();

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
        }

        // Enrol the logged in teacher as well, so they may view the course details

        Enrolment::create([
            'user_id' => Auth::user()->id,
            'course_id' => $c->id,
            'workshop_id' => $w[0]->id

        ]);


        session()->flash('success', 'The course and assessments have been created successfully. The specified teachers and students have been created and/or enrolled.');
        return redirect("courses/$c->id");
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
