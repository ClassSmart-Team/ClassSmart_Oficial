<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\AssignmentRequest;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\File;
use App\Models\Notification;
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
            ->with(['group', 'unit'])
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
            ->with(['group', 'unit', 'files'])
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
 
        // Notificar a los alumnos del grupo automáticamente
        $assignment->load('group.students');
        $notification = Notification::create([
            'created_by'         => $request->user()->id,
            'title'              => 'Nueva tarea publicada',
            'message'            => "El maestro publicó una nueva tarea: {$assignment->title}",
            'type'               => 'General',
            'related_assignment' => $assignment->id,
            'related_group'      => $assignment->group_id,
        ]);
        $studentIds = $assignment->group->students->pluck('id');
        $notification->recipients()->attach($studentIds, ['read_at' => null]);
 
        $assignment->load(['group', 'unit', 'files']);
 
        return $this->successResponse(
            new AssignmentResource($assignment),
            'Tarea creada exitosamente',
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
            return $this->errorResponse('Tarea no encontrada o sin permisos para eliminarla', 404);
        }
        $assignment->delete();
        return $this->successResponse(null, 'Tarea eliminada exitosamente', 200);
    }
}