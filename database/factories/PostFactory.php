<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = $this->faker->sentence();
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => implode('</br>', $this->faker->paragraphs(5)),
            'content_short' => $this->faker->paragraph(3),
            'source_uri' => $this->faker->url(),
            'comment_disabled' => $this->faker->boolean(30),
            'status' => $this->faker->randomElements(['published','draft','trash'])[0],
            'display_time' => $this->faker->dateTimeBetween(now(), Carbon::now()->addDays(7)),
            'importance' => $this->faker->numberBetween(0, 3),
            'user_id' => 1

        ];
    }
}
