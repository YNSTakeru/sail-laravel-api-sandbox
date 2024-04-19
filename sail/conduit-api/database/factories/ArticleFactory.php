<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence,
            // 255文字以内のランダムな文字列を生成
            "description" => $this->faker->text(255),
            "body" => $this->faker->paragraph,
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
