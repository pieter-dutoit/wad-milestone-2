<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherRoleID = DB::table('roles')->get()->where('role', 'teacher')->first()->id;
        $studentRoleID = DB::table('roles')->get()->where('role', 'student')->first()->id;
        DB::table('users')->insert([
            [
                'name' => "admin",
                's_number' => "s12345",
                'role_id' => $teacherRoleID,
                'email' => 'admin@admin.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Benny",
                's_number' => "s123446",
                'role_id' => $studentRoleID,
                'email' => 'benny@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Sandy",
                's_number' => "s123447",
                'role_id' => $studentRoleID,
                'email' => 'sandy@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Jenny",
                's_number' => "s123448",
                'role_id' => $studentRoleID,
                'email' => 'jenny@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Randall",
                's_number' => "s123431",
                'role_id' => $studentRoleID,
                'email' => 'randall@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Henry",
                's_number' => "s123421",
                'role_id' => $studentRoleID,
                'email' => 'henry@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Dylan",
                's_number' => "s123499",
                'role_id' => $studentRoleID,
                'email' => 'dylan@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Eric",
                's_number' => "s123464",
                'role_id' => $studentRoleID,
                'email' => 'eric@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Lyle",
                's_number' => "s1234345",
                'role_id' => $studentRoleID,
                'email' => 'lyle@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Riana",
                's_number' => "s1234463",
                'role_id' => $studentRoleID,
                'email' => 'riana@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Martin",
                's_number' => "s134677",
                'role_id' => $studentRoleID,
                'email' => 'martin@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "John",
                's_number' => "s156447",
                'role_id' => $studentRoleID,
                'email' => 'john@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Jan",
                's_number' => "s1289747",
                'role_id' => $studentRoleID,
                'email' => 'jan@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Ben",
                's_number' => "s124347",
                'role_id' => $studentRoleID,
                'email' => 'ben@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Nick",
                's_number' => "s64563447",
                'role_id' => $studentRoleID,
                'email' => 'nick@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Terry",
                's_number' => "s1236447",
                'role_id' => $studentRoleID,
                'email' => 'terry@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ]
        ]);
    }
}
