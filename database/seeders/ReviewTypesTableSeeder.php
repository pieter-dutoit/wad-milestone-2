<?php

namespace Database\Seeders;

use App\Models\ReviewType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'type' => 'teacher_assign',
            ],
            [
                'type' => 'student_select',
            ]
        ];
        foreach ($types as $type) {
            ReviewType::create($type);
        }
    }
}
