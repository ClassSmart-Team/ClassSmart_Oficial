<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Mail\WelcomeUserMail;
use App\Models\Configuration;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use ApiResponse;

    // Login — devuelve token + usuario con rol
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::with('role')->where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('Credenciales incorrectas', 401);
        }

        if (!$user->active) {
            return $this->errorResponse('Tu cuenta está desactivada, contacta al administrador', 403);
        }

        $token = $user->createToken('token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user'  => new UserResource($user),
        ], 'Inicio de sesión exitoso', 200);
    }

    // Registro público — solo crea la cuenta, sin token
    // El usuario debe hacer login después
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'      => $data['name'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'cellphone' => $data['cellphone'],
            'role_id'   => $data['role_id'],
        ]);

        Configuration::create([
            'user_id' => $user->id,
        ]);

        Mail::to($user->email)->send(new WelcomeUserMail($user));

        return $this->successResponse(
            new UserResource($user),
            'Registro exitoso, ahora puedes iniciar sesión',
            201
        );
    }

    // Logout — elimina el token actual
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Sesión cerrada correctamente', 200);
    }

    // Me — devuelve el usuario autenticado con su rol
    public function me(Request $request)
    {
        $user = $request->user()->load('role');

        return $this->successResponse(new UserResource($user), 'Usuario autenticado', 200);
    }
}
