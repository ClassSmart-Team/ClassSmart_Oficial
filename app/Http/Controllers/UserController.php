<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
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
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
 
        $user = User::create($data);
        $user->load('role');
 
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
}