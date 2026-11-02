<?php

namespace Database\Factories;

use App\Models\Practical;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'practical_id' => Practical::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'sql_answer' => 'SELECT * FROM users;',
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}
