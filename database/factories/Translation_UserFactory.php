<?php

namespace Database\Factories;

use App\Models\Translation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation_User>
 */
class Translation_UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $translationIds = Translation::all()->pluck('id')->toArray();
        $userIds = User::all()->pluck('id')->toArray();

        return [
            'translation_id'=>$this->faker->randomElement($translationIds), 
            'user_id'=>$this->faker->randomElement($userIds),
        ];
    }
}
