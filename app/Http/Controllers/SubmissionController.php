<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\SubmissionRequest;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use App\Models\File;
use App\Models\Notification;
use App\Models\Submission;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
 
class SubmissionController extends Controller
{
    use ApiResponse;

    private function storeSubmissionAttachment(UploadedFile $uploadedFile, Submission $submission, int $userId): File
    {
        $filePath = $uploadedFile->store('submissions', 'public');

        if (!$filePath) {
            abort(500, 'No se pudo guardar el archivo de la entrega');
        }

        return File::create([
            'submission_id' => $submission->id,
            'user_id'       => $userId,
            'context'       => 'student_submission',
            'file_name'     => $uploadedFile->getClientOriginalName(),
            'file_path'     => $filePath,
            'type'          => $uploadedFile->getClientMimeType(),
            'size'          => $uploadedFile->getSize(),
        ]);
    }

    private function visibleSubmissionsQuery($user): Builder
    {
        $query = Submission::query();

        if ($user && $user->isAdmin()) {
            return $query;
        }

        if ($user && $user->isTeacher()) {
            return $query->whereHas('assignment.group', function ($groupQuery) use ($user) {
                $groupQuery->where('owner', $user->id);
            });
        }

        if ($user && $user->isStudent()) {
            return $query->where('student_id', $user->id);
        }

        return $query->whereRaw('1 = 0');
    }

    private function findAccessibleSubmission(int|string $id): ?Submission
    {
        $user = request()->user();

        return $this->visibleSubmissionsQuery($user)->find($id);
    }
 
    public function index(Request $request)
    {
        $query = $this->visibleSubmissionsQuery($request->user())
            ->with(['student', 'assignment', 'files']);

        if ($request->filled('assignment_id')) {
            $query->where('assignment_id', $request->integer('assignment_id'));
        }

        $submissions = $query->get();

        return $this->successResponse(
            SubmissionResource::collection($submissions),
            'Entregas obtenidas exitosamente',
            200
        );
    }

    public function mySubmissions(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->isStudent()) {
            return $this->errorResponse('No tienes permisos para consultar esta ruta', 403);
        }

        $query = Submission::query()
            ->where('student_id', $user->id)
            ->with(['student', 'assignment', 'files']);

        if ($request->filled('assignment_id')) {
            $query->where('assignment_id', $request->integer('assignment_id'));
        }

        $submissions = $query->get();

        return $this->successResponse(
            SubmissionResource::collection($submissions),
            'Mis entregas obtenidas exitosamente',
            200
        );
    }

    public function mySubmissionShow($id)
    {
        $user = request()->user();

        if (!$user || !$user->isStudent()) {
            return $this->errorResponse('No tienes permisos para consultar esta ruta', 403);
        }

        $submission = Submission::query()
            ->where('student_id', $user->id)
            ->with(['student', 'assignment', 'files'])
            ->find($id);

        if (!$submission) {
            return $this->errorResponse('Entrega no encontrada o no te pertenece', 404);
        }

        return $this->successResponse(
            new SubmissionResource($submission),
            'Mi entrega obtenida exitosamente',
            200
        );
    }

    public function myAssignmentSubmissions(Request $request, $assignmentId)
    {
        $user = $request->user();

        if (!$user || !$user->isStudent()) {
            return $this->errorResponse('No tienes permisos para consultar esta ruta', 403);
        }

        $assignment = Assignment::with('group', 'unit')
            ->whereKey($assignmentId)
            ->whereHas('group.students', function ($studentsQuery) use ($user) {
                $studentsQuery
                    ->where('users.id', $user->id)
                    ->where('student_groups.active', true);
            })
            ->first();

        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada o no perteneces al grupo', 404);
        }

        $submissions = Submission::query()
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $user->id)
            ->with(['student', 'assignment', 'files'])
            ->get();

        return $this->successResponse(
            [
                'assignment' => new AssignmentResource($assignment),
                'submissions' => SubmissionResource::collection($submissions),
                'has_submission' => $submissions->isNotEmpty(),
            ],
            'Mis entregas de la tarea obtenidas exitosamente',
            200
        );
    }
 
    public function store(SubmissionRequest $request)
    {
        $assignment = Assignment::with('group')->find($request->assignment_id);
        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada', 404);
        }

        $student = $request->user();
        $isEnrolled = $assignment->group
            ? $assignment->group->students()
                ->where('users.id', $student->id)
                ->where('student_groups.active', true)
                ->exists()
            : false;

        if (!$isEnrolled) {
            return $this->errorResponse('No puedes entregar tareas de un grupo donde no estas inscrito', 403);
        }

        $alreadySubmitted = Submission::query()
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->exists();

        if ($alreadySubmitted) {
            return $this->errorResponse('Ya realizaste una entrega para esta tarea', 422);
        }

        if (!$assignment->isActive()) {
            return $this->errorResponse('Esta tarea ya no acepta entregas', 422);
        }
        $submissionDate = now();
        $isLate = $submissionDate > $assignment->end_date;
        $submission = Submission::create([
            'assignment_id'   => $request->assignment_id,
            'student_id'      => $student->id,
            'submission_date' => $submissionDate,
            'status'          => $isLate ? 'Entregada tarde' : 'Entregada',
        ]);
        // Guardar archivos adjuntos si vienen
        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $uploadedFiles = array_merge($uploadedFiles, is_array($files) ? $files : [$files]);
        }

        if ($request->hasFile('file')) {
            $uploadedFiles[] = $request->file('file');
        }

        foreach ($uploadedFiles as $uploadedFile) {
            $this->storeSubmissionAttachment($uploadedFile, $submission, $student->id);
        }
        // Notificar al maestro automáticamente
        $notification = Notification::create([
            'created_by'         => $student->id,
            'title'              => 'Tarea entregada',
            'message'            => "{$student->name} entregó la tarea: {$assignment->title}" . ($isLate ? ' (tarde)' : ''),
            'type'               => 'Individual',
            'related_assignment' => $assignment->id,
        ]);
        // getAttribute('owner') para obtener el ID del maestro y no el objeto
        $notification->recipients()->attach(
            $assignment->group->getAttribute('owner'),
            ['read_at' => null]
        );
        $submission->load(['student', 'assignment', 'files']);
        return $this->successResponse(
            new SubmissionResource($submission),
            'Tarea entregada exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $submission = $this->findAccessibleSubmission($id);
        if (!$submission) {
            return $this->errorResponse('Entrega no encontrada o sin permisos para verla', 404);
        }

        $submission->load(['student', 'assignment', 'files']);

        return $this->successResponse(
            new SubmissionResource($submission),
            'Entrega obtenida exitosamente',
            200
        );
    }
 
    // Calificar una entrega — solo maestros
    public function grade(Request $request, $id)
    {
        $request->validate([
            'grade'    => ['required', 'numeric', 'min:0', 'max:10'],
            'feedback' => ['nullable', 'string'],
        ]);

        $submission = $this->visibleSubmissionsQuery($request->user())
            ->with(['student', 'assignment'])
            ->find($id);

        if (!$submission) {
            return $this->errorResponse('Entrega no encontrada o sin permisos para calificarla', 404);
        }

        $submission->update([
            'grade'    => $request->grade,
            'feedback' => $request->feedback,
            'status'   => 'Calificada',
        ]);
        // Notificar al alumno que fue calificado
        $student = $submission->student;
        $notification = Notification::create([
            'created_by'         => $request->user()->id,
            'title'              => 'Entrega calificada',
            'message'            => "Tu entrega de {$submission->assignment->title} fue calificada con {$request->grade}",
            'type'               => 'Individual',
            'related_assignment' => $submission->assignment_id,
        ]);
        $notification->recipients()->attach($student->id, ['read_at' => null]);
        // Notificar a los padres del alumno
        Notification::notifyParentsOf($student, [
            'created_by'         => $request->user()->id,
            'title'              => 'Actividad calificada',
            'message'            => "{$student->name} recibió calificación en: {$submission->assignment->title} — Nota: {$request->grade}",
            'type'               => 'Individual',
            'related_assignment' => $submission->assignment_id,
        ]);
        $submission->load(['student', 'assignment', 'files']);
        return $this->successResponse(
            new SubmissionResource($submission),
            'Entrega calificada exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $submission = $this->findAccessibleSubmission($id);
        if (!$submission) {
            return $this->errorResponse('Entrega no encontrada o sin permisos para eliminarla', 404);
        }
        $submission->delete();
        return $this->successResponse(null, 'Entrega eliminada exitosamente', 200);
    }
}