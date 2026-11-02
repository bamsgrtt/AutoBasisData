<?php

namespace Database\Factories;

use App\Models\Execution;
use App\Models\Screenshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Screenshot>
 */
class ScreenshotFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'execution_id' => Execution::factory(),
            'file_path' => 'screenshots/'.fake()->uuid().'.png',
            'thumbnail_path' => 'screenshots/thumbs/'.fake()->uuid().'.png',
        ];
    }
}
