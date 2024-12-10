<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Snack>
 */
class SnackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title_ru' => fake()->text(10),
            'description_ru' => fake()->text(),
            'price' => rand(1000, 10000),
            'link' => 'https://www.pngegg.com/en/png-iylzz',
            'high_image_link' => 'https://e7.pngegg.com/pngimages/284/947/png-clipart-smiley-desktop-happiness-face-smiley-miscellaneous-face.png',
            'low_image_link' => 'https://e7.pngegg.com/pngimages/284/947/png-clipart-smiley-desktop-happiness-face-smiley-miscellaneous-face.png',
            'status' => 'IN_PROCESS',
        ];
    }
}
