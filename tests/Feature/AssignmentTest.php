<?php

use App\Models\Assignment;
use App\Models\Group;
use App\Models\Period;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\PeriodSeeder;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(UserSeeder::class);
    $this->seed(PeriodSeeder::class);
});

// ─── Helpers ───────────────────────────────────────────────────────────────

function createGroup(User $teacher, Period $period): Group
{
    return Group::create([
        'owner'       => $teacher->id,
        'period_id'   => $period->id,
        'name'        => 'Grupo Test',
        'description' => 'Descripción de prueba',
        'active'      => true,
    ]);
}

function createUnit(Group $group): Unit
{
    return Unit::create([
        'group_id'    => $group->id,
        'name'        => 'Unidad Test',
        'description' => 'Descripción de unidad',
        'start_date'  => now()->toDateString(),
        'end_date'    => now()->addDays(7)->toDateString(),
    ]);
}

function createAssignment(Group $group, Unit $unit): Assignment
{
    return Assignment::create([
        'group_id'    => $group->id,
        'unit_id'     => $unit->id,
        'title'       => 'Tarea Original',
        'description' => 'Descripción original',
        'start_date'  => now()->toDateString(),
        'end_date'    => now()->addDays(7)->toDateString(),
        'status'      => 'Activa',
    ]);
}

// ─── INDEX ─────────────────────────────────────────────────────────────────

test('Admin Route Assignments', function () {
    $admin = User::first();

    $this->actingAs($admin)
        ->getJson('api/assignments')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

test('Teacher Route Assignments', function () {
    $teacher = User::where('email', 'laura@gmail.com')->first();

    $this->actingAs($teacher)
        ->getJson('api/assignments')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

test('Student Forbidden Route Assignments', function () {
    $student = User::where('email', 'emiliano@gmail.com')->first();

    $this->actingAs($student)
        ->getJson('api/assignments')
        ->assertStatus(403);
});

// ─── STORE ─────────────────────────────────────────────────────────────────

test('Admin Can Create Assignment', function () {
    $admin   = User::first();
    $teacher = User::where('email', 'laura@gmail.com')->first();
    $period  = Period::first();
    $group   = createGroup($teacher, $period);
    $unit    = createUnit($group);

    $this->actingAs($admin)
        ->postJson('api/assignments', [
            'group_id'    => $group->id,
            'unit_id'     => $unit->id,
            'title'       => 'Tarea Admin',
            'description' => 'Descripción',
            'start_date'  => now()->toDateString(),
            'end_date'    => now()->addDays(5)->toDateString(),
            'status'      => 'Activa',
        ])
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'title']
        ]);

    $this->assertDatabaseHas('assignments', [
        'title' => 'Tarea Admin',
    ]);
});

test('Teacher Can Create Assignment', function () {
    $teacher = User::where('email', 'laura@gmail.com')->first();
    $period  = Period::first();
    $group   = createGroup($teacher, $period);
    $unit    = createUnit($group);

    $this->actingAs($teacher)
        ->postJson('api/assignments', [
            'group_id'    => $group->id,
            'unit_id'     => $unit->id,
            'title'       => 'Tarea Teacher',
            'description' => 'Descripción',
            'start_date'  => now()->toDateString(),
            'end_date'    => now()->addDays(5)->toDateString(),
            'status'      => 'Activa',
        ])
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'title']
        ]);
});


// ─── DESTROY ───────────────────────────────────────────────────────────────

test('Teacher Can Cancel Own Assignment', function () {
    $teacher = User::where('email', 'laura@gmail.com')->first();
    $period  = Period::first();
    $group   = createGroup($teacher, $period);
    $unit    = createUnit($group);
    $assignment = createAssignment($group, $unit);

    $this->actingAs($teacher)
        ->deleteJson("api/assignments/{$assignment->id}")
        ->assertOk();
});

// ─── MY ASSIGNMENTS ────────────────────────────────────────────────────────

test('Student Can Access My Assignments', function () {
    $student = User::where('email', 'emiliano@gmail.com')->first();

    $this->actingAs($student)
        ->getJson('api/my-assignments')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

