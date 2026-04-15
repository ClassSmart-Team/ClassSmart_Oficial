<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignmentRequest;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\File;
use App\Models\Notification;
use App\Models\Submission;
use App\Notifications\AssignmentNotification;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class AssignmentController extends Controller
{
    use ApiResponse;

    private function storeAssignmentAttachment(UploadedFile $uploadedFile, Assignment $assignment, int $userId): File
    {
        $filePath = $uploadedFile->store('assignments', 'public');

        if (!$filePath) {
            abort(500, 'No se pudo guardar el archivo de la tarea');
        }

        return File::create([
            'assignment_id' => $assignment->id,
            'user_id'       => $userId,
            'context'       => 'assignment_material',
            'file_name'     => $uploadedFile->getClientOriginalName(),
            'file_path'     => $filePath,
            'type'          => $uploadedFile->getClientMimeType(),
            'size'          => $uploadedFile->getSize(),
        ]);
    }

    private function visibleAssignmentsQuery($user): Builder
    {
        $query = Assignment::query();

        if ($user && $user->isAdmin()) {
            return $query;
        }

        if ($user && $user->isTeacher()) {
            return $query->whereHas('group', function ($groupQuery) use ($user) {
                $groupQuery->where('owner', $user->id);
            });
        }

        if ($user && $user->isStudent()) {
            return $query->whereHas('group.students', function ($studentsQuery) use ($user) {
                $studentsQuery
                    ->where('users.id', $user->id)
                    ->where('student_groups.active', true);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    private function findAccessibleAssignment(int|string $id): ?Assignment
    {
        $user = request()->user();

        return $this->visibleAssignmentsQuery($user)->find($id);
    }

    public function index(Request $request)
    {
        $assignments = $this->visibleAssignmentsQuery($request->user())
            ->with(['group', 'unit'])
            ->withCount('submissions')
            ->get();
        return $this->successResponse(
            AssignmentResource::collection($assignments),
            'Tareas obtenidas exitosamente',
            200
        );
    }

    public function myAssignments(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->isStudent()) {
            return $this->errorResponse('No tienes permisos para consultar esta ruta', 403);
        }

        $assignments = Assignment::query()
            ->whereHas('group.students', function ($studentsQuery) use ($user) {
                $studentsQuery
                    ->where('users.id', $user->id)
                    ->where('student_groups.active', true);
            })
            ->with(['group.ownerUser', 'unit'])
            ->withCount('submissions')
            ->get();

        return $this->successResponse(
            AssignmentResource::collection($assignments),
            'Mis tareas obtenidas exitosamente',
            200
        );
    }

    public function myAssignmentShow($id)
    {
        $user = request()->user();

        if (!$user || !$user->isStudent()) {
            return $this->errorResponse('No tienes permisos para consultar esta ruta', 403);
        }

        $assignment = Assignment::query()
            ->whereKey($id)
            ->whereHas('group.students', function ($studentsQuery) use ($user) {
                $studentsQuery
                    ->where('users.id', $user->id)
                    ->where('student_groups.active', true);
            })
            ->with(['group', 'unit', 'files', "submissions" => function ($submissionQuery) use ($user) {
                $submissionQuery->where('student_id', $user->id)->with('files');
            }])
            ->withCount('submissions')
            ->first();

        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada o no perteneces al grupo', 404);
        }

        return $this->successResponse(
            new AssignmentResource($assignment),
            'Mi tarea obtenida exitosamente',
            200
        );
    }

    public function store(AssignmentRequest $request)
    {
        $data = $request->validated();
        unset($data['files']);
        $assignment = Assignment::create($data);

        // Guardar archivos adjuntos del maestro
        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $uploadedFiles = array_merge($uploadedFiles, is_array($files) ? $files : [$files]);
        }

        if ($request->hasFile('file')) {
            $uploadedFiles[] = $request->file('file');
        }

        foreach ($uploadedFiles as $uploadedFile) {
            $this->storeAssignmentAttachment($uploadedFile, $assignment, $request->user()->id);
        }

        if ($request->hasFile('file')) {
            $uploadedFiles[] = $request->file('file');
        }

        foreach ($uploadedFiles as $uploadedFile) {
            $this->storeAssignmentAttachment($uploadedFile, $assignment, $request->user()->id);
        }

        // Notificar a los alumnos del grupo automáticamente
        $assignment->load(['group.students.parents.configuration', 'group.students.configuration', 'unit', 'files']);
        $notification = Notification::create([
            'created_by'         => $request->user()->id,
            'title'              => 'Nueva tarea publicada',
            'message'            => "El maestro publicó una nueva tarea: {$assignment->title}",
            'type'               => 'General',
            'related_assignment' => $assignment->id,
            'related_group'      => $assignment->group_id,
        ]);
        $students = $assignment->group->students;
        $notification->recipients()->attach($students->pluck('id'), ['read_at' => null]);

        // Cambia la carga de relaciones antes de los foreach
        $group = $assignment->group;

        // NOTIFICACIONES PUSH Y EMAIL
        foreach ($group->students as $student) {
            $student->notify(new AssignmentNotification($assignment, $student));

            foreach ($student->parents as $parent) {
                $parent->notify(new AssignmentNotification($assignment, $student));
            }
        }

        return $this->successResponse(
            new AssignmentResource($assignment),
            'Tarea y notificaciones creadas exitosamente',
            201
        );
    }

    public function show($id)
    {
        $assignment = $this->findAccessibleAssignment($id);

        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada o sin permisos para verla', 404);
        }

        $assignment->loadCount('submissions')
        ->load(["submissions","submissions.student", "submissions.files", "group", "unit", "files"]);

        return $this->successResponse(
            new AssignmentResource($assignment),
            'Tarea obtenida exitosamente',
            200
        );
    }

    public function update(AssignmentRequest $request, $id)
    {
        $assignment = $this->findAccessibleAssignment($id);

        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada o sin permisos para editarla', 404);
        }
        $data = $request->validated();
        unset($data['files']);
        $assignment->update($data);

        // Agregar nuevos archivos si vienen
        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $uploadedFiles = array_merge($uploadedFiles, is_array($files) ? $files : [$files]);
        }

        if ($request->hasFile('file')) {
            $uploadedFiles[] = $request->file('file');
        }

        foreach ($uploadedFiles as $uploadedFile) {
            $this->storeAssignmentAttachment($uploadedFile, $assignment, $request->user()->id);
        }

        if ($request->hasFile('file')) {
            $uploadedFiles[] = $request->file('file');
        }

        foreach ($uploadedFiles as $uploadedFile) {
            $this->storeAssignmentAttachment($uploadedFile, $assignment, $request->user()->id);
        }

        $assignment->load(['group', 'unit', 'files']);

        return $this->successResponse(
            new AssignmentResource($assignment),
            'Tarea actualizada exitosamente',
            200
        );
    }

    public function destroy($id)
    {
        $assignment = $this->findAccessibleAssignment($id);

        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada o sin permisos para cancelarla', 404);
        }

        $assignment->status = 'Cancelada';
        $assignment->save();

        return $this->successResponse(null, 'Tarea cancelada exitosamente', 200);
    }

    /* PARENT */
    public function getParentAssignments()
    {
        $user = auth('sanctum')->user();

        if (!$user->isParent()) {
            return $this->errorResponse('Acceso denegado.', 403);
        }

        $children = $user->children()->with([
            'groups.units',
            'groups.assignments' => function($q) {
                $q->where('status', '!=', 'Cancelada')->with(['submissions']);
            }
        ])->get();

        $allActivities = [];

        foreach ($children as $child) {
            foreach ($child->groups as $group) {
                foreach ($group->assignments as $task) {
                    $unit = $group->units->firstWhere('id', $task->unit_id);
                    $unitName = $unit ? $unit->name : 'Sin Unidad';

                    $allActivities[] = $this->formatActivityResponse($task, $child, $group, (object)[
                        'id' => $task->unit_id,
                        'name' => $unitName
                    ]);
                }
            }
        }

        return $this->successResponse($allActivities, 'Actividades obtenidas exitosamente');
    }

    /**
     * Función auxiliar para estandarizar la respuesta
     */
    private function formatActivityResponse($task, $child, $group, $unit)
    {
        if (!$task) {
            return [
                'id'         => null, // Indica que es una unidad vacía
                'childId'    => $child->id,
                'child'      => $child->name,
                'subject'    => $group->name,
                'unit_id'    => $unit->id,
                'unit_name'  => $unit->name,
                'status'     => 'vacio',
                'title'      => null,
            ];
        }

        $submission = $task->submissions->firstWhere('student_id', $child->id);
        $status = 'Pendiente';

        if ($task->status === 'Cerrada' && !$submission) {
            $status = 'Atrasada';
        } elseif ($submission) {
            $isLate = $task->end_date && $submission->submission_date > $task->end_date;
            if ($submission->grade !== null) {
                $status = 'Calificada';
            } else {
                $status = $isLate ? 'Tardia' : 'Entregada';
            }
        } elseif ($task->end_date && now() > $task->end_date) {
            $status = 'Atrasada';
        }

        return [
            'id'         => $task->id,
            'childId'    => $child->id,
            'child'      => $child->name,
            'title'      => $task->title,
            'subject'    => $group->name,
            'end_date'   => $task->end_date,
            'unit_id'    => $unit->id,
            'unit_name'  => $unit->name,
            'status'     => $status,
            'submission' => $submission ? [
                'status' => $submission->status,
                'grade'  => $submission->grade,
            ] : null,
        ];
    }

    public function getParentAssignmentDetail(Request $request, $assignmentId)
    {
        $user = auth('sanctum')->user();

        if ($user->role_id !== 4) {
            return $this->errorResponse('Acceso denegado.', 403);
        }

        $childId = $request->query('child_id');

        if (!$childId) {
            return $this->errorResponse('El ID del hijo es requerido para ver el detalle específico.', 400);
        }

        // 2. Validar que el childId realmente pertenezca a este padre
        $isMyChild = $user->children()->where('users.id', $childId)->exists();
        if (!$isMyChild) {
            return $this->errorResponse('No tienes permiso para ver la información de este estudiante.', 403);
        }

        $assignment = Assignment::with([
            'group.ownerUser',
            'files' => function($q) {
                $q->where('context', 'assignment_material');
            }
        ])->find($assignmentId);

        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada', 404);
        }

        $submission = Submission::with('files')
            ->where('assignment_id', $assignmentId)
            ->where('student_id', $childId)
            ->first();

        $teacher = $assignment->group->ownerUser;

        $resources = [];
        foreach ($assignment->files as $f) {
            $resources[] = [
                'id'   => $f->id,
                'name' => $f->file_name,
                'type' => $f->file_type,
                'size' => $f->size
            ];
        }

        $submissionData = null;
        if ($submission) {
            $submissionFiles = [];
            foreach ($submission->files as $sf) {
                $submissionFiles[] = [
                    'id'   => $sf->id,
                    'name' => $sf->file_name,
                    'type' => $sf->file_type,
                    'size' => $sf->size
                ];
            }

            $submissionData = [
                'status'          => $submission->status,
                'grade'           => $submission->grade,
                'feedback'        => $submission->feedback,
                'submission_date' => $submission->submission_date ? $submission->submission_date->toIso8601String() : null,
                'files'           => $submissionFiles
            ];
        }

        $data = [
            'id'          => $assignment->id,
            'title'       => $assignment->title,
            'description' => $assignment->description,
            'subject'     => $assignment->group->name,
            'child_name'  => $user->children()->find($childId)->name, // Añadimos esto para el frontend
            'teacher'     => $teacher ? ($teacher->name . ' ' . $teacher->lastname) : 'Docente',
            'end_date'    => $assignment->end_date ? $assignment->end_date->toIso8601String() : null,
            'points'      => 100,
            'resources'   => $resources,
            'submission'  => $submissionData
        ];

        return $this->successResponse($data, 'Detalle de tarea obtenido');
    }
}
