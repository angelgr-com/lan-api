<?php

namespace Database\Seeders;

use App\Models\Learn_User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LearnUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Learn_User::factory()->times(10)->create();
    }
}
