<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $users = User::with('role')->get();
        return $this->successResponse(UserResource::collection($users), 'Usuarios obtenidos exitosamente', 200);
    }

    public function store(UserRequest $request)
    {
        // Validación extra para student_id sin tocar UserRequest
        $request->validate([
            'student_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->load('role');

        // Si es padre y viene student_id, vincular al hijo
        if ($user->role_id === 4 && $request->student_id) {
            $user->children()->attach($request->student_id);
        }

        return $this->successResponse(new UserResource($user), 'Usuario creado exitosamente', 201);
    }

    public function show($id)
    {
        $user = User::with('role')->find($id);

        if (!$user) {
            return $this->errorResponse('Usuario no encontrado', 404);
        }

        return $this->successResponse(new UserResource($user), 'Usuario obtenido exitosamente', 200);
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('Usuario no encontrado', 404);
        }
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        $user->load('role');

        return $this->successResponse(new UserResource($user), 'Usuario actualizado exitosamente', 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('Usuario no encontrado', 404);
        }
        // Soft delete: desactivar en lugar de eliminar
        $user->active = false;
        $user->save();
        return $this->successResponse(null, 'Usuario desactivado exitosamente', 200);
    }

    /*------------- PARENT --------------------*/
    public function getProfile($id = null)
    {
        $user = $id ? User::find($id) : auth('sanctum')->user();

        if (!$user) {
            return $this->errorResponse('Usuario no encontrado', 404);
        }

        $relations = ['role'];
        if ($user->role_id === 4) {
            $relations[] = 'children';
        }

        $user->load($relations);

        return $this->successResponse(new UserResource($user), 'Usuario obtenido exitosamente', 200);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('sanctum')->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'cellphone' => 'nullable|string',
            'password' => 'exclude_if:password,null|exclude_if:password,""|min:8'
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $this->successResponse(new UserResource($user), 'Perfil actualizado', 200);
    }

    public function getMyChildren()
    {
        $user = auth('sanctum')->user();

        if ($user->role_id !== 4) {
            return $this->errorResponse('Acceso denegado. No eres un padre.', 403);
        }

        $children = $user->children()->with(['role', 'groups.period'])->get();

        return $this->successResponse(UserResource::collection($children), 'Hijos obtenidos exitosamente', 200);
    }

    /*NOTIFICACIONES PUSH Y EMAILS*/
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $request->user()->update([
            'fcm_token' => $request->fcm_token
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'FCM Token actualizado correctamente.'
        ]);
    }
}
