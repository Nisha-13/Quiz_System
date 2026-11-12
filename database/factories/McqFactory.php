<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Mcq;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mcq>
 */
class McqFactory extends Factory
{
    protected $model = Mcq::class;

    public function definition(): array
    {
        return [
            'question' => fake()->sentence(),
            'option_a' => fake()->word(),
            'option_b' => fake()->word(),
            'option_c' => fake()->word(),
            'option_d' => fake()->word(),
            'correct_ans' => fake()->randomElement(['a', 'b', 'c', 'd']),
            'admin_id' => Admin::factory(),
            'quiz_id' => Quiz::factory(),
        ];
    }

    /**
     * Keep the question in the same category as the quiz it belongs to.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Mcq $mcq) {
            $mcq->category_id = Quiz::find($mcq->quiz_id)?->category_id;
        });
    }
}
