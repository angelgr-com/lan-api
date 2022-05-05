<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
			['level' => 'easy'],
			['level' => 'medium'],
            ['level' => 'hard'],
        ];
 
        DB::table('difficulties')->insert($difficulties);
    }
}
