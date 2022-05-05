<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
			['id' => Str::uuid(), 'type' => 'admin'],
			['id' => Str::uuid(), 'type' => 'student'],
            ['id' => Str::uuid(), 'type' => 'teacher'],
        ];
 
        DB::table('roles')->insert($roles);
    }
}
