<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Cefr;
use App\Models\Difficulty;
use App\Models\Source;
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
        $cefrIds = Cefr::all()->pluck('id')->toArray();
        $difficulty = ['easy', 'medium', 'hard'];
        $sourceIds = Source::all()->pluck('id')->toArray();
        $typeIds = Type::all()->pluck('id')->toArray();

        return [
            'text'=>$this->faker->sentence(),
            'cefr_id'=>$this->faker->randomElement($cefrIds),
            'difficulty'=>$difficulty[rand(0, 2)], 
            'source_id'=>$this->faker->randomElement($sourceIds), 
            'type_id'=>$this->faker->randomElement($typeIds),
        ];
    }
}
