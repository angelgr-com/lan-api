<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Source>
 */
class SourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $authorIds = Author::all()->pluck('id')->toArray();

        return [
            'title'=>$this->faker->sentence(),
            'chapter'=>$this->faker->randomNumber(2, false),
            'paragraph'=>$this->faker->randomNumber(2, false),
            'url'=>$this->faker->url(),
            'author_id'=>$this->faker->randomElement($authorIds),
        ];
    }
}
