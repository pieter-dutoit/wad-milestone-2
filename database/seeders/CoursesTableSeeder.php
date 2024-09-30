<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'course_code' => '7005ICT',
                'course_name' => 'Web App Dev',
            ],
            [
                'course_code' => '7100ICT',
                'course_name' => 'Programming Principles',
            ],
            [
                'course_code' => '7205ICT',
                'course_name' => 'Systems Design',
            ]
        ];
        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
