<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cefrs = [
			['id' => Str::uuid(), 'type' => 'poetry'],
			['id' => Str::uuid(), 'type' => 'quote'],
        ];
 
        DB::table('types')->insert($cefrs);
    }
}
