<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'category_id' => Category::factory(),
        ];
    }
}
