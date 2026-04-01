<?php
 
namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
 
class GroupController extends Controller
{
    use ApiResponse;

    private function userCanViewAllGroups($user): bool
    {
        return $user && $user->isAdmin();
    }

    private function groupsVisibleToUser($user): Builder
    {
        $query = Group::query();

        if ($this->userCanViewAllGroups($user)) {
            return $query;
        }

        if ($user && $user->isTeacher()) {
            return $query->where('owner', $user->id);
        }

        if ($user && $user->isStudent()) {
            return $query->whereHas('students', function ($students) use ($user) {
                $students
                    ->where('users.id', $user->id)
                    ->where('student_groups.active', true);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    private function findAccessibleGroup(int|string $id): ?Group
    {
        $user = request()->user();

        return $this->groupsVisibleToUser($user)->find($id);
    }
 
    public function index()
    {
        $user = request()->user();
        $query = $this->groupsVisibleToUser($user)
            ->with(['ownerUser', 'period'])
            ->withCount(['students', 'assignments']);

        $groups = $query->get();

        return $this->successResponse(
            GroupResource::collection($groups),
            'Grupos obtenidos exitosamente',
            200
        );
    }

    public function myGroups()
    {
        $user = request()->user();

        if (!$user || !$user->isStudent()) {
            return $this->errorResponse('No tienes permisos para consultar esta ruta', 403);
        }

        $groups = Group::query()
            ->whereHas('students', function ($students) use ($user) {
                $students
                    ->where('users.id', $user->id)
                    ->where('student_groups.active', true);
            })
            ->with(['ownerUser', 'period'])
            ->withCount(['students', 'assignments'])
            ->get();

        return $this->successResponse(
            GroupResource::collection($groups),
            'Mis grupos obtenidos exitosamente',
            200
        );
    }

    public function myGroupShow($id)
    {
        $user = request()->user();

        if (!$user || !$user->isStudent()) {
            return $this->errorResponse('No tienes permisos para consultar esta ruta', 403);
        }

        $group = Group::query()
            ->whereKey($id)
            ->whereHas('students', function ($students) use ($user) {
                $students
                    ->where('users.id', $user->id)
                    ->where('student_groups.active', true);
            })
            ->first();

        if (!$group) {
            return $this->errorResponse('Grupo no encontrado o no estas inscrito en el', 404);
        }

        $group->load(['ownerUser', 'period', 'units', 'students', 'assignments', 'schedules']);
        $group->loadCount(['students', 'assignments']);

        return $this->successResponse(
            new GroupResource($group),
            'Mi grupo obtenido exitosamente',
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