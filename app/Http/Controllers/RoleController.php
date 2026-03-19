<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Traits\ApiResponse;
 
class RoleController extends Controller
{
    use ApiResponse;
 
    public function index()
    {
        $roles = Role::all();
        return $this->successResponse(
            RoleResource::collection($roles),
            'Roles obtenidos exitosamente',
            200
        );
    }
 
    public function store(RoleRequest $request)
    {
        $role = Role::create($request->validated());
        return $this->successResponse(
            new RoleResource($role),
            'Rol creado exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->errorResponse('Rol no encontrado', 404);
        }
        return $this->successResponse(
            new RoleResource($role),
            'Rol obtenido exitosamente',
            200
        );
    }
 
    public function update(RoleRequest $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->errorResponse('Rol no encontrado', 404);
        }
        $role->update($request->validated());

        return $this->successResponse(
            new RoleResource($role),
            'Rol actualizado exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->errorResponse('Rol no encontrado', 404);
        }
        $role->delete();
 
        return $this->successResponse(null, 'Rol eliminado exitosamente', 200);
    }
}