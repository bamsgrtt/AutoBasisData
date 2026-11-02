<?php

namespace Database\Seeders;

use App\Models\Execution;
use App\Models\Practical;
use App\Models\Report;
use App\Models\Screenshot;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $practical = Practical::factory()->create([
            'user_id' => $user->id,
            'name' => 'Praktikum Basis Data 1',
            'selected_database' => 'autobasisdata',
            'status' => 'in_progress',
        ]);

        $task = Task::factory()->create([
            'practical_id' => $practical->id,
            'title' => 'Tampilkan Semua User',
            'description' => 'Tulis query SQL untuk menampilkan seluruh data dari tabel users.',
            'sql_answer' => 'SELECT * FROM users;',
            'order' => 1,
        ]);

        $execution = Execution::factory()->create([
            'task_id' => $task->id,
            'sql_command' => 'SELECT * FROM users;',
            'output' => json_encode([['id' => $user->id, 'name' => $user->name, 'email' => $user->email]]),
            'is_correct' => true,
        ]);

        Screenshot::factory()->create([
            'execution_id' => $execution->id,
        ]);

        Report::factory()->create([
            'practical_id' => $practical->id,
            'score' => 100,
            'summary' => 'Semua tugas diselesaikan dengan benar.',
        ]);
    }
}
