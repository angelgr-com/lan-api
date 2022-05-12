<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Source::factory()->times(10)->create();
        $sources = [
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Locke',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Locke')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Tolkien',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Tolkien')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Dumas',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Dumas')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Rohn',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Rohn')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io King',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'King')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Carnegie',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Carnegie')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Dylan',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Dylan')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Churchill',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Churchill')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Eliot',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Eliot')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Sharma',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Sharma')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Tolle',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Tolle')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Maraboli',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Maraboli')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Dickens',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Dickens')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Cameron',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Cameron')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Emerson',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Emerson')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Deng',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Deng')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Beecher',
                'author_id' => DB::table('authors')
                ->where('last_name', '=', 'Beecher')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'title' => null,
                'chapter' => null,
                'paragraph' => null,
                'url' => 'zenquotes.io Dogen',
                'author_id' => DB::table('authors')
                ->where('first_name', '=', 'Dogen')->value('id'),
            ],
        ];

        DB::table('sources')->insert($sources);
    }
}
