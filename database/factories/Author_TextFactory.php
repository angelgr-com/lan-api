<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Text;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author_Text>
 */
class Author_TextFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $authorIds = Author::all()->pluck('id')->toArray();
        $textIds = Text::all()->pluck('id')->toArray();

        return [
            'author_id'=>$this->faker->randomElement($authorIds), 
            'text_id'=>$this->faker->randomElement($textIds),
        ];
    }
}
