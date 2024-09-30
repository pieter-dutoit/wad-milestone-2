<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Submission;
use Dotenv\Parser\Entry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entries = [];

        $assessments = Assessment::all();

        // For each assessment, find the related enrolments.
        foreach ($assessments as $assessment) {
            $course = Course::find(1);
            $enrolments = $course->enrolments()->get();

            $enrolments =  Enrolment::join('users', 'enrolments.user_id', '=', 'users.id')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.role', 'student')
                ->where('enrolments.course_id', $course->id)
                ->where('course_id', $course->id)
                ->select('enrolments.user_id')
                ->get();

            // For each enrolment, create a new submission.
            foreach ($enrolments as $enrolment) {
                $entries[] = [
                    'score' => null,
                    'assessment_id' => $assessment->id,
                    'student_id' => $enrolment->user_id,
                ];
            }
        }

        foreach ($entries as $entry) {
            Submission::create($entry);
        }
    }
}
