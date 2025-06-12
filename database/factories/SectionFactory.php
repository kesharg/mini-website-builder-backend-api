<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\SectionType;
use App\Models\Section;

class SectionFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(SectionType::getValues());

        $content = match ($type) {
            SectionType::HEADER => ['text' => $this->faker->sentence()],
            SectionType::TEXT   => ['text' => $this->faker->paragraph()],
            SectionType::HTML   => ['text' => '<p>' . $this->faker->sentence() . '</p>'],
            SectionType::IMAGE => [
                'url' => 'https://picsum.photos/seed/' . $this->faker->uuid . '/640/480',
            ],
            default => [],
        };

        return [
            'page_id' => Page::factory(),
            'type' => $type,
            'content' => $content,
            'position' => $this->faker->numberBetween(0, 10),
        ];
    }
}
