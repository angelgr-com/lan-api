<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Cefr;
use App\Models\Difficulty;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Text>
 */
class TextFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $authorIds = Author::all()->pluck('id')->toArray();
        $cefrIds = Cefr::all()->pluck('id')->toArray();
        $difficultyIds = Difficulty::all()->pluck('id')->toArray();
        $typeIds = Type::all()->pluck('id')->toArray();

        return [
            'text'=>$this->faker->sentence(),
            'author_id'=>$this->faker->randomElement($authorIds), 
            'cefr_id'=>$this->faker->randomElement($cefrIds),
            'difficulty_id'=>$this->faker->randomElement($difficultyIds), 
            'type_id'=>$this->faker->randomElement($typeIds),
        ];
    }
}
