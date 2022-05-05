<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author_Source>
 */
class Author_SourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $authorIds = Author::all()->pluck('id')->toArray();
        $sourceIds = Source::all()->pluck('id')->toArray();

        return [
            'author_id'=>$this->faker->randomElement($authorIds), 
            'source_id'=>$this->faker->randomElement($sourceIds),
        ];
    }
}
