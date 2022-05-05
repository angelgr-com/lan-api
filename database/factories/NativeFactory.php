<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Native>
 */
class NativeFactory extends Factory
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
