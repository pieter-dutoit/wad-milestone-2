<?php

namespace Database\Seeders;

use App\Models\Workshop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkshopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workshops = [
            [
                'day' => 'Monday',
                'location' => 'Online',
            ],
            [
                'day' => 'Monday',
                'location' => 'GC 42.1.23',
            ],
            [
                'day' => 'Tuesday',
                'location' => 'GC 42.1.23',
            ],
            [
                'day' => 'Tuesday',
                'location' => 'NA 16.2.2',
            ]
        ];

        foreach ($workshops as $key => $workshop) {
            Workshop::create($workshop);
        }
    }
}
