<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'property_id' => $this->faker->numberBetween(1, 10),
            'name' => $this->faker->randomElement(['price', 'rooms', 'bathrooms', 'area']),
            'value' => $this->faker->numberBetween(100, 1000),
        ];
    }
}
