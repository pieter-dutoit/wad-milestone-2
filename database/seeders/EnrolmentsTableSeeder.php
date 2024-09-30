<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrolmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherRoleID = DB::table('roles')->get()->where('role', 'teacher')->first()->id;
        $studentRoleID = DB::table('roles')->get()->where('role', 'student')->first()->id;

        $students = DB::table('users')->where('role_id', $studentRoleID)->get();
        $teacher = DB::table('users')->where('role_id', $teacherRoleID)->get()->first();

        $workshops = DB::table('workshops')->limit(2)->get();
        $courseID = DB::table('courses')->get()->where('course_code', '7005ICT')->first()->id;

        foreach ($students as $index => $student) {
            DB::table('enrolments')->insert([
                [
                    'user_id' => $student->id,
                    'course_id' => $courseID,
                    'workshop_id' => $workshops[$index % 2 == 0 ? 1 : 0]->id,
                ]
            ]);
        }

        DB::table('enrolments')->insert([
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
        ]);
    }
}
