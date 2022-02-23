<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SearchProfileFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'search_profile_id' => $this->faker->numberBetween(1, 10),
            'name' =>$this->faker->randomElement(['price', 'rooms', 'bathrooms', 'area']),
            'min_value' =>$this->faker->numberBetween(100, 1000),
            'max_value' =>$this->faker->numberBetween(100, 1000),
            'value_type' => $this->faker->randomElement(['direct', 'range']),
        ];
    }
}
