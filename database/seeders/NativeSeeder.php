<?php

namespace Database\Seeders;

use App\Models\Native;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Native::factory()->times(10)->create();
    }
}
