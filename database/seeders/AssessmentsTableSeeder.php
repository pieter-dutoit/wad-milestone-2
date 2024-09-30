<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = DB::table('reviewtypes')->get();
        $courseID = DB::table('courses')->get()->where('course_code', '7005ICT')->first()->id;
        foreach ($types as $index => $type) {
            DB::table('assessments')->insert([
                [
                    'title' => "Week $index Peer Review",
                    'instruction' => 'Lorem ipsum',
                    'due_date' => '2024-11-11 23:59:00',
                    'num_reviews' => 2,
                    'max_score' => 10,
                    'course_id' => $courseID,
                    'type_id' => $type->id,
                ]
            ]);
        }
    }
}
