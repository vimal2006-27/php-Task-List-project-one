<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_index_displays_tasks(): void
    {
        $task = Task::factory()->create([
            'title' => 'Sample Task',
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertSee('Sample Task');
    }

    public function test_tasks_create_screen_can_be_rendered(): void
    {
        $response = $this->get(route('tasks.create'));

        $response->assertStatus(200);
        $response->assertSee('Add Task');
    }

    public function test_new_task_can_be_created(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'New Feature Task',
            'description' => 'Short description here',
            'long_description' => 'Detailed long description here',
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Feature Task',
            'description' => 'Short description here',
            'long_description' => 'Detailed long description here',
        ]);

        $task = Task::where('title', 'New Feature Task')->first();
        $response->assertRedirect(route('tasks.show', ['id' => $task->id]));
    }

    public function test_tasks_show_displays_task_details(): void
    {
        $task = Task::factory()->create([
            'title' => 'My Specific Task',
            'description' => 'A unique description',
            'long_description' => 'A unique long description',
        ]);

        $response = $this->get(route('tasks.show', ['id' => $task->id]));

        $response->assertStatus(200);
        $response->assertSee('My Specific Task');
        $response->assertSee('A unique description');
        $response->assertSee('A unique long description');
    }

    public function test_tasks_show_returns_404_for_missing_task(): void
    {
        $response = $this->get(route('tasks.show', ['id' => 99999]));

        $response->assertNotFound();
    }
}
