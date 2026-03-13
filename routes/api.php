<?php

use App\Http\Controllers\GroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Route para UserController en Android Studio
Route::prefix('users')->group(function () {
Route::get('/', [UserController::class, 'readAll']);
Route::post("/create", [UserController::class, "create"]);
Route::get("/{id}", [UserController::class, "readOne"]);
Route::put("/{id}", [UserController::class, "update"]);
Route::delete("/{id}", [UserController::class, "delete"]);
});

//ROUTE PARA REGISTRO Y LOGIN
Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [UserController::class, "create"]);
Route::Apiresource('groups', GroupController::class);
Route::Apiresource('group/create', GroupController::class);
