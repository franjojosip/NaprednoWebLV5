<?php

namespace Database\Seeders;

use App\Models\StudyType;
use Illuminate\Database\Seeder;

class StudyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StudyType::create(['name' => 'Strucni']);
        StudyType::create(['name' => 'Preddiplomski']);
        StudyType::create(['name' => 'Diplomski']);
    }
}
