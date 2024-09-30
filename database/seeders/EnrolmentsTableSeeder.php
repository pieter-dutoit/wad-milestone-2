<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Role;
use App\Models\Workshop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnrolmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherRole = Role::where('role', 'teacher')->first();
        $studentRole = Role::where('role', 'student')->first();
        $students = $studentRole->users()->get();
        $teacher = $teacherRole->users()->first();

        $workshops = Workshop::limit(2)->get();
        $courseID = Course::find(1)->id;

        foreach ($students as $index => $student) {
            Enrolment::create(
                [
                    'user_id' => $student->id,
                    'course_id' => $courseID,
                    'workshop_id' => $workshops[$index % 2 == 0 ? 1 : 0]->id,
                ]
            );
        }

        $teacherEnrolments = [
            [
                'user_id' => $teacher->id,
                'course_id' => $courseID,
                'workshop_id' => $workshops[0]->id,
            ],
            [
                'user_id' => $teacher->id,
                'course_id' => $courseID,
                'workshop_id' => $workshops[1]->id,
            ]
        ];

        foreach ($teacherEnrolments as $enrolment) {
            Enrolment::create($enrolment);
        }
    }
}
