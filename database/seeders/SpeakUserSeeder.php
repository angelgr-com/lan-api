<?php

namespace Database\Seeders;

use App\Models\Speak_User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpeakUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Speak_User::factory()->times(10)->create();
    }
}
