<?php

namespace Database\Seeders;

use App\Models\Author_Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthorSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Author_Source::factory()->times(10)->create();
        $author__sources = [
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Locke')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Locke')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Tolkien')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Tolkien')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Dumas')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Dumas')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Rohn')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Rohn')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'King')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io King')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Carnegie')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Carnegie')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Dylan')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Dylan')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Churchill')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Churchill')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Eliot')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Eliot')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Sharma')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Sharma')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Tolle')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Tolle')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Maraboli')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Maraboli')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Dickens')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Dickens')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Cameron')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Cameron')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Emerson')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Emerson')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Deng')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Deng')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('last_name', '=', 'Beecher')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Beecher')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'author_id' => DB::table('authors')->where('first_name', '=', 'Dogen')->value('id'),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Dogen')->value('id'),
            ],
        ];

        DB::table('author__sources')->insert($author__sources);
    }
}
