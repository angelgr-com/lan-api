<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Author::factory()->times(10)->create();
        $authors = [
            [
                'id' => Str::uuid(),
                'first_name' => 'John',
                'last_name' => 'Locke',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'J.R.R.',
                'last_name' => 'Tolkien',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Alexandre',
                'last_name' => 'Dumas',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Jim',
                'last_name' => 'Rohn',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Stephen',
                'last_name' => 'King',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Dale',
                'last_name' => 'Carnegie',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Bob',
                'last_name' => 'Dylan',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Winston',
                'last_name' => 'Churchill',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'John',
                'last_name' => 'Eliot',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Robin',
                'last_name' => 'Sharma',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Eckhart',
                'last_name' => 'Tolle',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Steve',
                'last_name' => 'Maraboli',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Charles',
                'last_name' => 'Dickens',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'James',
                'last_name' => 'Cameron',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Ralph Waldo',
                'last_name' => 'Emerson',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Ming-Dao',
                'last_name' => 'Deng',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Henry Ward',
                'last_name' => 'Beecher',
            ],
            [
                'id' => Str::uuid(),
                'first_name' => 'Dogen',
                'last_name' => '',
            ],
        ];

        DB::table('authors')->insert($authors);
    }
}
