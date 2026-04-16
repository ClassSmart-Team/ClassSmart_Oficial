<?php

use App\Models\Period;
use App\Models\User;
use App\Models\Notification;
use App\Models\Group;
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

test("Student can get their own notifications", function () {
    $student = User::where('email', 'jimena@gmail.com')->first();
    $teacher = User::where('email', 'laura@gmail.com')->first();

    $notification = Notification::create([
        'created_by' => $teacher->id,
        'title' => 'Calificación Disponible',
        'message' => 'Tu nota de Matemáticas ha sido publicada',
        'type' => 'Individual',
    ]);

    $student->receivedNotifications()->attach($notification->id, ['read_at' => null]);

    $response = $this->actingAs($student)
        ->getJson("api/notifications")
        ->assertOk()
        ->assertJsonFragment(["title" => "Calificación Disponible"]);
});

test("User cannot see notifications from other users", function () {
    $jimena = User::where('email', 'jimena@gmail.com')->first();
    $emiliano = User::where('email', 'emiliano@gmail.com')->first();
    $teacher = User::where('email', 'laura@gmail.com')->first();

    $notification = Notification::create([
        'created_by' => $teacher->id,
        'title' => 'Secreto de Emiliano',
        'message' => 'Solo para Emiliano',
        'type' => 'Individual',
    ]);

    $emiliano->receivedNotifications()->attach($notification->id, ['read_at' => null]);

    $response = $this->actingAs($jimena)
        ->getJson("api/notifications")
        ->assertOk();

    $response->assertJsonMissing(["title" => "Secreto de Emiliano"]);
});

test("Teacher can create general notification for a group", function () {
    $teacher = User::where('email', 'laura@gmail.com')->first();
    $period = Period::first();

    $group = Group::create([
        'owner' => $teacher->id,
        'period_id' => $period->id,
        'name' => 'Grupo Test',
        'active' => true
    ]);

    $response = $this->actingAs($teacher)
        ->postJson("api/notifications", [
            "title" => "Aviso General",
            "message" => "Mañana no hay clases",
            "type" => "General",
            "related_group" => $group->id
        ])
        ->assertCreated();

    $this->assertDatabaseHas("notifications", ["title" => "Aviso General"]);
});

test("User can mark a notification as read", function () {
    $student = User::where('email', 'jimena@gmail.com')->first();
    $teacher = User::where('email', 'laura@gmail.com')->first();

    $notification = Notification::create([
        'created_by' => $teacher->id,
        'title' => 'Test Leído',
        'message' => 'Contenido',
        'type' => 'Individual'
    ]);

    $student->receivedNotifications()->attach($notification->id, ['read_at' => null]);

    $response = $this->actingAs($student)
        ->patchJson("api/notifications/{$notification->id}/read")
        ->assertOk();

    $this->assertDatabaseMissing("notification_user", [
        "user_id" => $student->id,
        "notification_id" => $notification->id,
        "read_at" => null
    ]);
});

test("Student forbidden from creating notifications", function () {
    $student = User::where('email', 'jimena@gmail.com')->first();

    $response = $this->actingAs($student)
        ->postJson("api/notifications", [
            "title" => "Hack",
            "message" => "Intento crear notif",
            "type" => "General"
        ])
        ->assertForbidden();
});

test("Teacher can delete a notification", function () {
    $teacher = User::where('email', 'laura@gmail.com')->first();

    $notification = Notification::create([
        'created_by' => $teacher->id,
        'title' => 'Para borrar',
        'message' => 'Adiós',
        'type' => 'Individual'
    ]);

    $response = $this->actingAs($teacher)
        ->deleteJson("api/notifications/{$notification->id}")
        ->assertOk();

    $this->assertDatabaseMissing("notifications", ["id" => $notification->id]);
});

test("User can mark all their notifications as read at once", function () {
    $student = User::where('email', 'jimena@gmail.com')->first();
    $teacher = User::where('email', 'laura@gmail.com')->first();

    $notif1 = Notification::create(['created_by' => $teacher->id, 'title' => 'N1', 'message' => 'M1', 'type' => 'Individual']);
    $notif2 = Notification::create(['created_by' => $teacher->id, 'title' => 'N2', 'message' => 'M2', 'type' => 'Individual']);

    $student->receivedNotifications()->attach([$notif1->id, $notif2->id], ['read_at' => null]);

    $response = $this->actingAs($student)
        ->patchJson("api/notifications/read-all")
        ->assertOk();

    $unreadCount = DB::table('notification_user')
        ->where('user_id', $student->id)
        ->whereNull('read_at')
        ->count();

    expect($unreadCount)->toBe(0);
});
