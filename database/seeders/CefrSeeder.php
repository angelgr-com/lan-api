<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CefrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('cefrs')->truncate();
 
        $cefrs = [
			['id' => Str::uuid(), 'level' => 'A1'],
			['id' => Str::uuid(), 'level' => 'A2'],
            ['id' => Str::uuid(), 'level' => 'B1'],
			['id' => Str::uuid(), 'level' => 'B2'],
			['id' => Str::uuid(), 'level' => 'C1'],
            ['id' => Str::uuid(), 'level' => 'C2'],
        ];
 
        DB::table('cefrs')->insert($cefrs);
    }
}
