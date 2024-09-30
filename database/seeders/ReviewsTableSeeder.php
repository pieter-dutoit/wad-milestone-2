<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Enrolment;
use App\Models\Review;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assessments = Assessment::all();

        foreach ($assessments as $assessment) {
            // $type = $assessment->type()->get()->first()->type;
            // https://laravel.com/docs/11.x/eloquent-relationships#relationship-methods-vs-dynamic-properties
            $type = $assessment->type->type;
            $submissions = $assessment->submissions;


            if ($type == 'student_select') {
                foreach ($submissions as $submission) {
                    // Create reviews without reviewees
                    for ($i = 0; $i < $assessment->num_reviews; $i++) {
                        Review::create([
                            'reviewee_id' => null,
                            'submission_id' => $submission->id
                        ]);
                    }
                }
            }

            if ($type == 'teacher_assign') {
                // https://laravel.com/docs/11.x/eloquent-relationships#querying-relationship-existence
                // https://stackoverflow.com/questions/30231862/laravel-eloquent-has-with-wherehas-what-do-they-mean
                $enrolments = Enrolment::where('course_id', $assessment->course_id)
                    ->whereHas('user.role', function ($query) {
                        $query->where('role', '=', 'student');
                    })->get();

                // Groups students for reviews, if teacher_assigned set
                $allGroups = [];
                // Groups enrolments based on workshops
                $studentsByWorkshop = [];
                foreach ($enrolments as $index => $enrolment) {
                    $studentsByWorkshop[$enrolment->workshop_id][] = $enrolment;
                }

                // Break students in workshops into groups
                foreach ($studentsByWorkshop as $workshopStudents) {
                    $workshopGroups = [];
                    shuffle($workshopStudents);
                    $groupSize = $assessment->num_reviews + 1;
                    $numGroups = intVal(floor(count($workshopStudents) / $groupSize));

                    // Create at least one group
                    for ($i = 0; $i < ($numGroups > 0 ? $numGroups : 1); $i++) {
                        $workshopGroups[] = [];
                    }

                    foreach ($workshopStudents as $index => $student) {
                        $groupIndex = $index % count($workshopGroups);
                        $workshopGroups[$groupIndex][] = $student;
                    }

                    $allGroups = array_merge($allGroups, $workshopGroups);
                }

                // Create review based on groups
                foreach ($allGroups as $group) {
                    echo count($group);
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
                // End
            }
        }
    }
}
