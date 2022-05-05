<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CountrySeeder::class,
            LanguageSeeder::class,
            TypeSeeder::class,
            UserSeeder::class,
            CefrSeeder::class,
            AuthorSeeder::class,
            SourceSeeder::class,
            TextSeeder::class,
            AuthorSourceSeeder::class,
            EsTextSeeder::class,
            StudentSeeder::class,
            NativeSeeder::class,
            TranslationSeeder::class,
            TranslationUserSeeder::class,
        ]);
    }
}
