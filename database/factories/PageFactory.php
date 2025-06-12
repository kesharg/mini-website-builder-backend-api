<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(2);
        return [
            'title' => $title,
            'slug' => str()->slug($title),
        ];
    }
}
