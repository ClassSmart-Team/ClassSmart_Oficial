<?php

use App\Models\Group;
use App\Models\Period;
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

test("Admin Route Groups", function () {
    $admin = User::first();

    $response = $this->actingAs($admin)
        ->getJson("api/groups")
        ->assertOk()
        ->assertJsonFragment([
            "message" => "Grupos obtenidos exitosamente"
        ])
        ->assertJsonIsObject();
});

test("Teacher Route Groups", function () {
    $teacher = User::where("email", "laura@gmail.com")->first();

    $response = $this->actingAs($teacher)
        ->getJson("api/groups")
        ->assertOk()
        ->assertJsonFragment([
            "message" => "Grupos obtenidos exitosamente"
        ])
        ->assertJsonIsObject();
});

test("Student Forbidden Route Groups", function () {
    $student = User::where("email", "emiliano@gmail.com")->first();

    $response = $this->actingAs($student)
        ->getJson("api/groups")
        ->assertForbidden()
        ->assertJsonFragment([
            "message" => "Acceso denegado",
            "error" => "No tienes permisos para realizar esta acción"
        ])
        ->assertJsonIsObject();
});

test("Admin Can Create Group", function () {
    $admin = User::first();
    $teacher = User::where("email", "laura@gmail.com")->first();
    $period = Period::first();

    $response = $this->actingAs($admin)
        ->postJson("api/groups", [
            "owner" => $teacher->id,
            "period_id" => $period->id,
            "name" => "Grupo Test",
            "description" => "Descripcion de prueba",
            "active" => true
        ])
        ->assertCreated()
        ->assertJsonFragment([
            "message" => "Grupo creado exitosamente",
            "name" => "Grupo Test"
        ])
        ->assertJsonIsObject();

    $this->assertDatabaseHas("groups", [
        "name" => "Grupo Test",
        "owner" => $teacher->id,
        "period_id" => $period->id
    ]);
});

test("Teacher Can Create Group", function () {
    $teacher = User::where("email", "laura@gmail.com")->first();
    $period = Period::first();

    $response = $this->actingAs($teacher)
        ->postJson("api/groups", [
            "owner" => $teacher->id,
            "period_id" => $period->id,
            "name" => "Grupo Teacher",
            "description" => "Grupo creado por teacher",
            "active" => true
        ])
        ->assertCreated()
        ->assertJsonFragment([
            "message" => "Grupo creado exitosamente",
            "name" => "Grupo Teacher"
        ])
        ->assertJsonIsObject();

    $this->assertDatabaseHas("groups", [
        "name" => "Grupo Teacher",
        "owner" => $teacher->id,
        "period_id" => $period->id
    ]);
});

test("Group Validation", function () {
    $admin = User::first();

    $response = $this->actingAs($admin)
        ->postJson("api/groups", [])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            "period_id",
        ]);
});

test("Teacher Can Update Own Group", function () {
    $teacher = User::where("email", "laura@gmail.com")->first();
    $period = Period::first();

    $group = Group::create([
        "owner" => $teacher->id,
        "period_id" => $period->id,
        "name" => "Grupo Original",
        "description" => "Descripcion original",
        "active" => true
    ]);

    $response = $this->actingAs($teacher)
        ->putJson("api/groups/{$group->id}", [
            "name" => "Grupo Editado",
            "description" => "Descripcion editada"
        ])
        ->assertOk()
        ->assertJsonFragment([
            "message" => "Grupo actualizado exitosamente",
        ])
        ->assertJsonIsObject();
});