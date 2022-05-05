<?php

namespace Database\Seeders;

use App\Models\Author_Text;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Author_Text::factory()->times(10)->create();
    }
}
