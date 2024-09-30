<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entries = [];

        $assessments = DB::table('assessments')
            ->join('reviewtypes', 'reviewtypes.id', '=', 'assessments.type_id')
            ->get();


        foreach ($assessments as $assessment) {
            // Find all students (possible reviewees) of this assessment
            $allStudents = DB::table('enrolments')
                ->join('users', 'enrolments.user_id', '=', 'users.id')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('enrolments.course_id', $assessment->course_id)
                ->where('roles.role', 'student')
                ->select('enrolments.user_id', 'enrolments.workshop_id', 'users.name')
                ->get();


            if ($assessment->type == 'student_select') {
                foreach ($allStudents as $student) {
                    $submissionID = DB::table('submissions')
                        ->where('student_id', $student->user_id)
                        ->where('assessment_id', $assessment->id)
                        ->get()
                        ->first()
                        ->id;
                    for ($i = 0; $i < $assessment->num_reviews; $i++) {
                        $entries[] =
                            [
                                'reviewee_id' => null,
                                'submission_id' => $submissionID
                            ];
                    }
                }
            }

            if ($assessment->type == 'teacher_assign') {
                // Group reviewees by workshop
                $studentsByWorkshop = [];
                foreach ($allStudents as $index => $student) {
                    $studentsByWorkshop[$student->workshop_id][] = $student;
                }

                // Randomly place workshop students into groups
                $allGroups = [];
                // Create review groups for all workshops
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

                // Create review entries for each student
                foreach ($allGroups as $group) {
                    foreach ($group as $reviewerIndex => $reviewer) {
                        $submissionID = DB::table('submissions')
                            ->where('student_id', $reviewer->user_id)
                            ->where('assessment_id', $assessment->id)
                            ->get()
                            ->first()
                            ->id;
                        foreach ($group as $revieweeIndex => $reviewee) {
                            if ($reviewerIndex != $revieweeIndex) {
                                $entries[] = [
                                    'reviewee_id' => $reviewee->user_id,
                                    'submission_id' => $submissionID
                                ];
                            }
                        }
                    }
                }
            }
        }

        DB::table('reviews')->insert($entries);

        // Find submissions and join assessment to get review count
        // $submissions = DB::table('submissions')
        //     ->join('assessments', 'submissions.assessment_id', '=', 'assessments.id')
        //     ->join('reviewtypes', 'reviewtypes.id', '=', 'assessments.type_id')
        //     ->select('submissions.id', 'submissions.student_id', 'assessments.num_reviews', 'reviewtypes.type', 'assessments.course_id')

        //     // ->where('roles.role', 'student')
        //     // ->where('enrolments.course_id', $courseID)
        //     // ->where('course_id', $courseID)
        //     // ->select('enrolments.user_id')
        //     ->get();

        // foreach ($submissions as $submission) {
        //     // Find all other students (possible reviewees) also enrolled in the same course
        //     $allStudents = DB::table('enrolments')
        //         ->join('users', 'enrolments.user_id', '=', 'users.id')
        //         ->join('roles', 'users.role_id', '=', 'roles.id')
        //         ->where('roles.role', 'student')
        //         ->where('enrolments.course_id', $submission->course_id)
        //         ->whereNot('enrolments.user_id', $submission->student_id)
        //         ->select('enrolments.user_id', 'enrolments.workshop_id', 'users.name')
        //         ->get();

        //     // Group reviewees by workshop
        //     $studentsByWorkshop = [];
        //     foreach ($allStudents as $index => $reviewee) {
        //         $studentsByWorkshop[$reviewee->workshop_id][] = $reviewee;
        //     }



        //     if ($submission->type == 'teacher_assign') {
        //         $allGroups = [];
        //         // Create review groups for all workshops
        //         foreach ($studentsByWorkshop as $reviewees) {
        //             $workshopGroups = [];
        //             shuffle($reviewees);
        //             $groupSize = $submission->num_reviews + 1;
        //             $numGroups = intVal(floor(count($reviewees) / $groupSize));

        //             // Create at least one group
        //             for ($i = 0; $i < ($numGroups > 0 ? $numGroups : 1); $i++) {
        //                 $workshopGroups[] = [];
        //             }

        //             foreach ($reviewees as $index => $reviewee) {
        //                 $groupIndex = $index % count($workshopGroups);
        //                 $workshopGroups[$groupIndex][] = $reviewee;
        //             }

        //             $allGroups = array_merge($allGroups, $workshopGroups);
        //         }


        //         foreach ($allGroups as $group) {
        //             foreach ($group as $index => $student) {
        //             }
        //         }
        //     }




        // for ($i = 0; $i < $submission->num_reviews; $i++) {
        //     $entries[] = [
        //         'submission_id' => $submission->id
        //     ];
        // }
        // }
    }
}
