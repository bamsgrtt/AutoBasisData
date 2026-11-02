<?php

namespace Database\Factories;

use App\Models\Practical;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'practical_id' => Practical::factory(),
            'score' => fake()->numberBetween(0, 100),
            'summary' => fake()->paragraph(),
            'generated_at' => now(),
        ];
    }
}
