<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('courses')->insert([[
            'course_code' => '7005ICT',
            'course_name' => 'Web App Dev',
        ], [
            'course_code' => '7100ICT',
            'course_name' => 'Programming Principles',
        ], [
            'course_code' => '7205ICT',
            'course_name' => 'Systems Design',
        ]]);
    }
}
