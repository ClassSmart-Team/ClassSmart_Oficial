<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

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
            ->with(['ownerUser', 'period', "units"])
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
            ->with(['ownerUser', 'period', 'units'])
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
        $authUser = $request->user();

        if ($authUser->isAdmin()) {
            $data['owner'] = $data['owner'] ?? $authUser->id;
        } else {
            $data['owner'] = $authUser->id;
        }

        $group = Group::create($data);
        $group->load(['ownerUser', 'period', 'units']);
        $group->loadCount(['students', 'assignments']);

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

        $data = $request->validated();
        $authUser = $request->user();

        if (!$authUser->isAdmin()) {
            unset($data['owner']);
        }

        $group->update($data);
        $group->load(['ownerUser', 'period', 'units']);
        $group->loadCount(['students', 'assignments']);

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

        $group->active = false;
        $group->save();

        return $this->successResponse(null, 'Grupo desactivado exitosamente', 200);
    }

    //consumir alumnos que no esten en un grupo en especifico
    public function getAvailableStudents(Request $request, $id)
    {
        // 1. Verificamos que el grupo exista primero
        $group = Group::find($id);
        if (!$group) {
            return $this->errorResponse('Grupo no encontrado o sin permisos para eliminarlo', 404);
        }

        // 2. Consultamos los alumnos disponibles
        $students = User::with('role')->
        where('role_id', 3) // Solo alumnos
        ->where('active', true)          // Solo alumnos activos (opcional pero recomendado)
        ->whereDoesntHave('groups', function ($query) use ($id) {
            $query->where('group_id', $id); // Que NO estén ya en este grupo específico
        })
            ->get(); // Traemos TODA LA INFORMACION

        return $this->successResponse(UserResource::collection($students),'Alumnos disponibles para inscripción recuperados', 200);
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

    /* PARENT */
    public function getParentGroups()
    {
        $user = auth('sanctum')->user();

        if ($user->role_id !== 4) {
            return $this->errorResponse('Acceso denegado.', 403);
        }

        $children = $user->children()->with([
            'groups.ownerUser',
            'groups.period'
        ])->get();

        $data = [];

        foreach ($children as $child) {
            $childData = [
                'childId'   => $child->id,
                'childName' => $child->name . ' ' . $child->lastname,
                'groups'    => []
            ];

            foreach ($child->groups as $group) {
                $childData['groups'][] = [
                    'id'                => $group->id,
                    'name'              => $group->name,
                    'description'       => $group->description,
                    'active'            => (bool)$group->active,
                    'owner' => [
                        'name'     => $group->ownerUser->name ?? 'Sin',
                        'lastname' => $group->ownerUser->lastname ?? 'Asignar',
                    ],
                    'period' => [
                        'name' => $group->period->name ?? 'N/A',
                        'year' => $group->period->year ?? 2026,
                    ],
                    'completed_tasks'   => $group->completed_tasks ?? 0,
                    'pending_tasks'     => $group->pending_tasks ?? 0,
                ];
            }

            $data[] = $childData;
        }

        return $this->successResponse($data, 'Grupos de los hijos obtenidos');
    }

    public function getParentGroupDetail(Request $request, $groupId)
    {
        $user = auth('sanctum')->user();
        $childId = $request->query('child_id');

        if (!$childId) {
            return $this->errorResponse('ID del hijo requerido', 400);
        }

        $exists = $user->children()
            ->where('users.id', $childId)
            ->whereHas('groups', function($q) use ($groupId) {
                $q->where('groups.id', $groupId);
            })->exists();

        if (!$exists) {
            return $this->errorResponse('No tienes permiso para ver este grupo.', 403);
        }

        $group = Group::with(['ownerUser', 'period', 'assignments.unit', 'announcements'])
            ->find($groupId);

        $units = [];
        foreach ($group->units as $unit) {
            $units[] = [
                'id'   => $unit->id,
                'name' => $unit->name,
            ];
        }

        $activities = [];
        foreach ($group->assignments as $task) {
            $submission = Submission::where('assignment_id', $task->id)
                ->where('student_id', $childId)
                ->first();

            $status = 'Pendiente';

            if ($task->status === 'Cerrada' && !$submission) {
                $status = 'Atrasado';
            }
            elseif ($submission) {
                $isLate = $task->end_date && $submission->submission_date > $task->end_date;
                if ($submission->grade !== null) {
                    $status = 'Calificado';
                } else {
                    $status = $isLate ? 'Tardia' : 'Entregado';
                }
            }
            elseif ($task->end_date && now() > $task->end_date) {
                $status = 'Atrasado';
            }

            $activities[] = [
                'id'      => $task->id,
                'childId' => $childId,
                'title'   => $task->title,
                'subject' => $group->name,
                'dueDate' => $task->end_date ? $task->end_date->format('d M') : 'S/F',
                'status'  => $status,

                'unit_id'   => $task->unit_id ?? 0,
                'unit_name' => $task->unit ? $task->unit->name : 'Sin Unidad',

                'submission' => $submission ? [
                    'status' => $submission->status,
                    'grade'  => $submission->grade,
                ] : null,
            ];
        }

        $teacherName = $group->ownerUser
            ? ($group->ownerUser->name . ' ' . $group->ownerUser->lastname)
            : 'Profesor';

        $forumPosts = [];
        foreach ($group->announcements as $post) {
            $forumPosts[] = [
                'id'               => $post->id,
                'title'            => $post->title,
                'message'          => $post->message,
                'created_at'       => $post->created_at,
                'group' => [
                    'owner' => [
                        'name'     => $group->ownerUser->name,
                        'lastname' => $group->ownerUser->lastname,
                    ]
                ],
                'attachment_path'  => $post->attachment_path,
                'attachment_name'  => $post->attachment_name,
            ];
        }

        $data = [
            'group' => [
                'id'          => $group->id,
                'name'        => $group->name,
                'description' => $group->description,
                'teacher'     => $teacherName,
            ],
            'units'       => $units,
            'activities'  => $activities,
            'forum_posts' => $forumPosts
        ];

        return $this->successResponse($data, 'Detalle del grupo obtenido');
    }
}
