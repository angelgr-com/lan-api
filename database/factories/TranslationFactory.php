<?php

namespace Database\Factories;

use App\Models\Text;
use App\Models\User;
use App\Models\Language;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $languageIds = Language::all()->pluck('id')->toArray();
        $textIds = Text::all()->pluck('id')->toArray();
        $userIds = User::all()->pluck('id')->toArray();

        return [
            'hit_rate'=>$this->faker->randomFloat(2, 0, 1),
            'text'=>$this->faker->sentence(),
            'language_id'=>$this->faker->randomElement($languageIds), 
            'text_id'=>$this->faker->randomElement($textIds),
            'user_id'=>$this->faker->randomElement($userIds), 
        ];
    }
}
