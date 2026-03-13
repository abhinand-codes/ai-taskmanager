<?php

use App\Models\Task;
use App\Models\User;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->user  = User::factory()->create(['role' => 'user']);
});

// ─── Authentication ───────────────────────────────────────────────

test('guests are redirected to login', function () {
    $this->get(route('tasks.index'))->assertRedirect(route('login'));
});

// ─── Index ────────────────────────────────────────────────────────

test('admin can view tasks index', function () {
    Task::factory()->count(3)->create(['created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->get(route('tasks.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Tasks/Index'));
});

test('regular user can view tasks index', function () {
    $this->actingAs($this->user)
        ->get(route('tasks.index'))
        ->assertOk();
});

// ─── Create ───────────────────────────────────────────────────────

test('admin can view create task form', function () {
    $this->actingAs($this->admin)
        ->get(route('tasks.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Tasks/Form'));
});

test('regular user cannot view create task form', function () {
    $this->actingAs($this->user)
        ->get(route('tasks.create'))
        ->assertForbidden();
});

// ─── Store ────────────────────────────────────────────────────────

test('admin can create a task', function () {
    $this->actingAs($this->admin)
        ->post(route('tasks.store'), [
            'title'       => 'New Test Task',
            'description' => 'Test description',
            'priority'    => 'high',
            'status'      => 'pending',
            'due_date'    => '2026-04-01',
            'assigned_to' => $this->user->id,
        ])
        ->assertRedirect(route('tasks.index'));

    $this->assertDatabaseHas('tasks', [
        'title'      => 'New Test Task',
        'priority'   => 'high',
        'created_by' => $this->admin->id,
    ]);
});

test('task creation requires title', function () {
    $this->actingAs($this->admin)
        ->post(route('tasks.store'), [
            'priority' => 'high',
            'status'   => 'pending',
        ])
        ->assertSessionHasErrors('title');
});

test('regular user cannot create a task', function () {
    $this->actingAs($this->user)
        ->post(route('tasks.store'), [
            'title'    => 'Unauthorized Task',
            'priority' => 'low',
            'status'   => 'pending',
        ])
        ->assertForbidden();
});

// ─── Show ─────────────────────────────────────────────────────────

test('admin can view any task', function () {
    $task = Task::factory()->create(['created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->get(route('tasks.show', $task))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Tasks/Show'));
});

test('user can view their assigned task', function () {
    $task = Task::factory()->create([
        'assigned_to' => $this->user->id,
        'created_by'  => $this->admin->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('tasks.show', $task))
        ->assertOk();
});

test('user cannot view unassigned task', function () {
    $task = Task::factory()->create([
        'assigned_to' => null,
        'created_by'  => $this->admin->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('tasks.show', $task))
        ->assertForbidden();
});

// ─── Update ───────────────────────────────────────────────────────

test('admin can update a task', function () {
    $task = Task::factory()->create(['created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->put(route('tasks.update', $task), [
            'title'    => 'Updated Title',
            'priority' => 'low',
            'status'   => 'completed',
        ])
        ->assertRedirect(route('tasks.index'));

    $this->assertDatabaseHas('tasks', [
        'id'    => $task->id,
        'title' => 'Updated Title',
    ]);
});

// ─── Delete ───────────────────────────────────────────────────────

test('admin can delete a task', function () {
    $task = Task::factory()->create(['created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->delete(route('tasks.destroy', $task))
        ->assertRedirect(route('tasks.index'));

    $this->assertSoftDeleted('tasks', ['id' => $task->id]);
});

test('regular user cannot delete a task', function () {
    $task = Task::factory()->create(['created_by' => $this->admin->id]);

    $this->actingAs($this->user)
        ->delete(route('tasks.destroy', $task))
        ->assertForbidden();
});

// ─── Status Update ────────────────────────────────────────────────

test('user can update status of assigned task', function () {
    $task = Task::factory()->create([
        'assigned_to' => $this->user->id,
        'created_by'  => $this->admin->id,
        'status'      => 'pending',
    ]);

    $this->actingAs($this->user)
        ->patch(route('tasks.status', $task->id), ['status' => 'in_progress'])
        ->assertOk()
        ->assertJson(['message' => 'Status updated successfully.']);

    $this->assertDatabaseHas('tasks', [
        'id'     => $task->id,
        'status' => 'in_progress',
    ]);
});