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
        $students = $studentRole->users()->limit(10)->get();
        $teacher = $teacherRole->users()->first();

        $workshops = Workshop::limit(4)->get();
        $courses = Course::limit(2)->get();

        foreach ($courses as $course) {

            foreach ($students as $index => $student) {

                Enrolment::create(
                    [
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'workshop_id' => $workshops[$index % 2 == 0 ? 1 : 0]->id,
                    ]
                );
            }
        }
        $teacherEnrolments = [
            [
                'user_id' => $teacher->id,
                'course_id' => $courses[0]->id,
                'workshop_id' => $workshops[0]->id,
            ],
            [
                'user_id' => $teacher->id,
                'course_id' => $courses[0]->id,
                'workshop_id' => $workshops[1]->id,
            ],
            [
                'user_id' => $teacher->id,
                'course_id' => $courses[1]->id,
                'workshop_id' => $workshops[2]->id,
            ],
            [
                'user_id' => $teacher->id,
                'course_id' => $courses[1]->id,
                'workshop_id' => $workshops[3]->id,
            ]
        ];

        foreach ($teacherEnrolments as $enrolment) {
            Enrolment::create($enrolment);
        }
    }
}
