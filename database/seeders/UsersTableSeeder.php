<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(['name' => 'Admin', 'email' => 'admin@mail.com', 'password' => bcrypt('admin'), 'role_id' => '1']);
        User::create(['name' => 'Student', 'email' => 'student@mail.com', 'password' => bcrypt('student'), 'role_id' => '2']);
        User::create(['name' => 'Teacher', 'email' => 'teacher@mail.com', 'password' => bcrypt('teacher'), 'role_id' => '3']);
    }
}
