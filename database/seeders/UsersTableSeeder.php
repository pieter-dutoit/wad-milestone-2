<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentRoleID = Role::where('role', 'student')->first()->id;
        $teacherRoleID = Role::where('role', 'teacher')->first()->id;

        $users = [
            [
                'name' => "Pieter",
                's_number' => "s12345",
                'role_id' => $teacherRoleID,
                'email' => 'admin@admin.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Jen",
                's_number' => "s12321",
                'role_id' => $teacherRoleID,
                'email' => 'admin@12321admin.com',
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
                'name' => "Mark",
                's_number' => "s123499",
                'role_id' => $studentRoleID,
                'email' => 'mark@fakelearner.com',
                'password' => bcrypt('Admin123'),
            ],
            [
                'name' => "Jacques",
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
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
