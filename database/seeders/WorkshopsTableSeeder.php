<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkshopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('workshops')->insert([
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
        ]);
    }
}
