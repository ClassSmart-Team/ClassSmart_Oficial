<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\UserSeeder;
use Database\Seeders\RoleSeeder;

uses(RefreshDatabase::class);
beforeEach(function (){
    $this->seed(RoleSeeder::class);
    $this->seed(UserSeeder::class);
});

test("Good Credentials", function (){
    $response = $this->postJson("api/login",[
        "email" => "laura@gmail.com",
        "password" => "yaweyes"
    ]);
    $response->assertOk()->assertJsonIsObject();
});

test("Bad Credentials", function (){
    $response = $this->postJson("api/login",[
        "email" => "laura@gmail.com",
        "password" => "lauralaura",
    ]);
    $response->assertUnauthorized()->assertJsonIsObject();
});

test("Admin Route", function(){
    $user = User::first();
    $response = $this->actingAs($user)->getJson("api/users")
    ->assertOk()->assertJsonFragment([
        "message" => "Usuarios obtenidos exitosamente"
    ])->assertJsonIsObject();
});

test("Teacher Route", function(){
    $user = User::where("email", "laura@gmail.com")->first();
    $response = $this->actingAs($user)->getJson("api/groups")->assertOk()->assertJsonFragment([
        "message" => "Grupos obtenidos exitosamente"
    ])->assertJsonIsObject();
});

test("Forbidden Route", function(){
    $user = User::where("email", "laura@gmail.com")->first();
    $response = $this->actingAs($user)->getJson("api/users")->assertForbidden()->assertJsonIsObject()->
    assertJsonFragment([
        "message" => "Acceso denegado",
        "error" => "No tienes permisos para realizar esta acción"
    ]);
});

test("Unauthenticated Route", function(){
    $response = $this->getJson("api/users")->assertUnauthorized();
});

test("Student Route", function(){
    $user = User::where("email", "emiliano@gmail.com")->first();
    $response = $this->actingAs($user)->getJson("api/my-groups")->assertOk()->assertJsonFragment([
        "message" => "Mis grupos obtenidos exitosamente"])->assertJsonIsObject();
});