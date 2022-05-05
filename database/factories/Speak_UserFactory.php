<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Speak_User>
 */
class Speak_UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $languageIds = Language::all()->pluck('id')->toArray();
        $userIds = User::all()->pluck('id')->toArray();

        return [
            'language_id'=>$this->faker->randomElement($languageIds), 
            'user_id'=>$this->faker->randomElement($userIds),
        ];
    }
}
