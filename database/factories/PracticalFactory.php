<?php

namespace Database\Factories;

use App\Models\Practical;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Practical>
 */
class PracticalFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'instructions' => fake()->paragraphs(2, true),
            'selected_database' => fake()->word(),
            'user_id' => User::factory(),
            'status' => 'draft',
        ];
    }
}
