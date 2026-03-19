<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
 
class GroupController extends Controller
{
    use ApiResponse;
 
    public function index()
    {
        $groups = Group::with(['owner', 'period'])
            ->withCount(['students', 'assignments'])
            ->get();
        return $this->successResponse(
            GroupResource::collection($groups),
            'Grupos obtenidos exitosamente',
            200
        );
    }
 
    public function store(GroupRequest $request)
    {
        $data = $request->validated();
        $group = Group::create([
            ...$data,
            'owner' => $request->user()->id, // el maestro autenticado es el dueño
        ]);
        $group->load(['owner', 'period']);
        return $this->successResponse(
            new GroupResource($group),
            'Grupo creado exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $group = Group::with(['owner', 'period', 'units', 'students', 'assignments', 'schedules'])
            ->withCount(['students', 'assignments'])
            ->find($id);
        if (!$group) {
            return $this->errorResponse('Grupo no encontrado', 404);
        }
        return $this->successResponse(
            new GroupResource($group),
            'Grupo obtenido exitosamente',
            200
        );
    }
 
    public function update(GroupRequest $request, $id)
    {
        $group = Group::find($id);
        if (!$group) {
            return $this->errorResponse('Grupo no encontrado', 404);
        }
        $group->update($request->validated());
        $group->load(['owner', 'period']);
 
        return $this->successResponse(
            new GroupResource($group),
            'Grupo actualizado exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return $this->errorResponse('Grupo no encontrado', 404);
        }
        $group->delete();
        return $this->successResponse(null, 'Grupo eliminado exitosamente', 200);
    }
 
    // Agregar alumno al grupo
    public function addStudent(Request $request, $id)
    {
        $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
        ]);
        $group = Group::find($id);
        if (!$group) {
            return $this->errorResponse('Grupo no encontrado', 404);
        }
        $group->students()->syncWithoutDetaching([$request->student_id]);
        return $this->successResponse(null, 'Alumno agregado al grupo exitosamente', 200);
    }
 
    // Remover alumno del grupo
    public function removeStudent(Request $request, $id)
    {
        $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
        ]);
        $group = Group::find($id);
        if (!$group) {
            return $this->errorResponse('Grupo no encontrado', 404);
        }
        $group->students()->detach($request->student_id);
        return $this->successResponse(null, 'Alumno removido del grupo exitosamente', 200);
    }
}