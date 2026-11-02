<?php

namespace Tests\Feature;

use App\Models\Execution;
use App\Models\Practical;
use App\Models\Report;
use App\Models\Screenshot;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoBasisDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads(): void
    {
        $this->get(route('home'))
            ->assertStatus(200)
            ->assertSee('Dashboard');
    }

    public function test_praktikum_index_loads(): void
    {
        $this->get(route('practicals.index'))
            ->assertStatus(200)
            ->assertSee('Praktikum');
    }

    public function test_settings_page_loads(): void
    {
        $this->get(route('settings'))
            ->assertStatus(200)
            ->assertSee('Pengaturan');
    }

    public function test_store_practical_and_parse_tasks(): void
    {
        $instructions = "1. Buat tabel mahasiswa\n2. Insert 3 data\n3. Tampilkan semua data";

        $response = $this->post(route('practicals.store'), [
            'name' => 'Praktikum SQL Dasar',
            'instructions' => $instructions,
            'selected_database' => 'autobasisdata',
        ]);

        $practical = Practical::where('name', 'Praktikum SQL Dasar')->first();
        $this->assertNotNull($practical);
        $this->assertEquals($instructions, $practical->instructions);
        $this->assertCount(3, $practical->tasks);
        $this->assertEquals('Buat tabel mahasiswa', $practical->tasks[0]->description);
        $response->assertRedirect(route('practicals.show', $practical));
    }

    public function test_workspace_loads(): void
    {
        $user = User::factory()->create();
        $practical = Practical::factory()->create(['user_id' => $user->id]);
        Task::factory()->create(['practical_id' => $practical->id]);

        $this->get(route('practicals.show', $practical))
            ->assertStatus(200)
            ->assertSee($practical->name)
            ->assertSee('Instruksi Praktikum');
    }

    public function test_save_execution_via_api(): void
    {
        $user = User::factory()->create();
        $practical = Practical::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['practical_id' => $practical->id]);

        $this->postJson(route('execution.save'), [
            'task_id' => $task->id,
            'sql_command' => 'SELECT 1;',
            'output' => '1',
            'is_correct' => true,
        ])->assertOk()
            ->assertJsonFragment(['status' => 'saved']);

        $this->assertDatabaseHas('executions', ['task_id' => $task->id, 'sql_command' => 'SELECT 1;']);
    }

    public function test_sql_execution_requires_database_selected(): void
    {
        $this->postJson(route('sql.execute'), ['sql_command' => 'SELECT 1;'])
            ->assertOk()
            ->assertJson(['error' => 'No database selected. Please configure a database first.']);
    }

    public function test_can_delete_draft_practical(): void
    {
        $practical = Practical::factory()->create(['status' => 'draft']);

        $response = $this->delete(route('practicals.destroy', $practical));

        $response->assertRedirect(route('practicals.index'));
        $this->assertDatabaseMissing('practicals', ['id' => $practical->id]);
    }

    public function test_cannot_delete_non_draft_practical(): void
    {
        $practical = Practical::factory()->create(['status' => 'completed']);

        $this->delete(route('practicals.destroy', $practical));

        $this->assertDatabaseHas('practicals', ['id' => $practical->id]);
    }

    public function test_model_relationships(): void
    {
        $user = User::factory()->create();
        $practical = Practical::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['practical_id' => $practical->id]);
        $execution = Execution::factory()->create(['task_id' => $task->id]);
        $screenshot = Screenshot::factory()->create(['execution_id' => $execution->id]);
        $report = Report::factory()->create(['practical_id' => $practical->id]);

        $this->assertTrue($user->practicals->contains($practical));
        $this->assertEquals($user->id, $practical->user->id);
        $this->assertTrue($practical->tasks->contains($task));
        $this->assertEquals($practical->id, $task->practical->id);
        $this->assertTrue($task->executions->contains($execution));
        $this->assertEquals($task->id, $execution->task->id);
        $this->assertTrue($execution->screenshots->contains($screenshot));
        $this->assertEquals($execution->id, $screenshot->execution->id);
        $this->assertEquals($practical->id, $report->practical->id);
        $this->assertEquals($report->id, $practical->report->id);
    }
}
