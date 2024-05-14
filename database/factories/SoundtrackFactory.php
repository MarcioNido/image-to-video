<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SoundtrackFactory extends Factory
{

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'path' => $this->faker->url,
        ];
    }
}
