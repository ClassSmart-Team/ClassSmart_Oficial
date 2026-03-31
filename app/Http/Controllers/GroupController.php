<?php
 
namespace App\Http\Controllers;
//comentario de prueba
use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
 
class GroupController extends Controller
{
    use ApiResponse;

    private function userCanViewAllGroups($user): bool
    {
        return $user && $user->isAdmin();
    }

    private function findAccessibleGroup(int|string $id): ?Group
    {
        $user = request()->user();
        $query = Group::query();

        // El admin puede acceder a cualquier grupo; el maestro solo a los suyos.
        if (!$this->userCanViewAllGroups($user)) {
            $query->where('owner', $user->id);
        }

        return $query->find($id);
    }
 
    public function index()
    {
        $user = request()->user();
        $query = Group::with(['ownerUser', 'period'])
            ->withCount(['students', 'assignments']);

        if (!$this->userCanViewAllGroups($user)) {
            $query->where('owner', $user->id);
        }

        $groups = $query->get();

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
        $group->load(['ownerUser', 'period']);
        return $this->successResponse(
            new GroupResource($group),
            'Grupo creado exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $group = $this->findAccessibleGroup($id);

        if (!$group) {
            return $this->errorResponse('Grupo no encontrado o sin permisos para verlo', 404);
        }

        $group->load(['ownerUser', 'period', 'units', 'students', 'assignments', 'schedules']);
        $group->loadCount(['students', 'assignments']);

        return $this->successResponse(
            new GroupResource($group),
            'Grupo obtenido exitosamente',
            200
        );
    }
 
    public function update(GroupRequest $request, $id)
    {
        $group = $this->findAccessibleGroup($id);
        if (!$group) {
            return $this->errorResponse('Grupo no encontrado o sin permisos para editarlo', 404);
        }
        $group->update($request->validated());
        $group->load(['ownerUser', 'period']);
 
        return $this->successResponse(
            new GroupResource($group),
            'Grupo actualizado exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $group = $this->findAccessibleGroup($id);
        if (!$group) {
            return $this->errorResponse('Grupo no encontrado o sin permisos para eliminarlo', 404);
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

        $group = $this->findAccessibleGroup($id);
        if (!$group) {
            return $this->errorResponse('Grupo no encontrado o sin permisos para editarlo', 404);
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

        $group = $this->findAccessibleGroup($id);
        if (!$group) {
            return $this->errorResponse('Grupo no encontrado o sin permisos para editarlo', 404);
        }

        $group->students()->detach($request->student_id);
        return $this->successResponse(null, 'Alumno removido del grupo exitosamente', 200);
    }
}