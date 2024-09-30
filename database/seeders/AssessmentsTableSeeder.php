<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\ReviewType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssessmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courseID = Course::find(1)->id;
        $types = ReviewType::all();

        foreach ($types as $index => $type) {
            Assessment::create(
                [
                    'title' => "Week $index Peer Review",
                    'instruction' => 'Lorem ipsum',
                    'due_date' => '2024-11-11 23:59:00',
                    'num_reviews' => 2,
                    'max_score' => 10,
                    'course_id' => $courseID,
                    'type_id' => $type->id,
                ]
            );
        }
    }
}
