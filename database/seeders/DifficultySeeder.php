<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DifficultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('difficulties')->truncate();
 
        $difficulties = [
			['id' => Str::uuid(), 'level' => 'easy'],
			['id' => Str::uuid(), 'level' => 'medium'],
            ['id' => Str::uuid(), 'level' => 'hard'],
        ];
 
        DB::table('difficulties')->insert($difficulties);
    }
}
