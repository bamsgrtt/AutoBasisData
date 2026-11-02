<?php

namespace Database\Factories;

use App\Models\Execution;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Execution>
 */
class ExecutionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'sql_command' => 'SELECT * FROM users;',
            'output' => json_encode([['id' => 1, 'name' => 'Test']]),
            'is_correct' => true,
            'executed_at' => now(),
        ];
    }
}
