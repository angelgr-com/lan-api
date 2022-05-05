<?php

namespace Database\Factories;

use App\Models\Text;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estext>
 */
class EstextFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $textIds = Text::all()->pluck('id')->toArray();
        
        return [
            'text'=>$this->faker->sentence(),
            'text_id'=>$this->faker->randomElement($textIds),
        ];
    }
}
