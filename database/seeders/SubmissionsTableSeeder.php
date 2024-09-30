<?php

namespace Database\Seeders;

use Dotenv\Parser\Entry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubmissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entries = [];

        $assessments = DB::table('assessments')->get();

        // For each assessment, find the related enrolments.
        foreach ($assessments as $assessment) {
            $courseID = DB::table('courses')->get()->where('id', $assessment->course_id)->first()->id;

            $enrolments = DB::table('enrolments')
                ->join('users', 'enrolments.user_id', '=', 'users.id')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.role', 'student')
                ->where('enrolments.course_id', $courseID)
                ->where('course_id', $courseID)
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

        DB::table('submissions')->insert($entries);
    }
}
