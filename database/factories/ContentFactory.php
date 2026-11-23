<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $postTitle = rtrim(ucwords(fake()->sentence()), '.');
        $postSlug = Str::slug($postTitle);

        return [
            'user_id'       => 1,
            'slug'          => $postSlug,
            'title'         => $postTitle,
            'excerpt'       => fake()->paragraph(3),
            'content'       => fake()->paragraph(9),
            'publish_time'  => fake()->unixTime(),
        ];
    }
}
