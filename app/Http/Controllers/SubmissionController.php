<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\SubmissionRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use App\Models\File;
use App\Models\Notification;
use App\Models\Submission;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
 
class SubmissionController extends Controller
{
    use ApiResponse;
 
    public function index()
    {
        $submissions = Submission::with(['student', 'assignment', 'files'])->get();
        return $this->successResponse(
            SubmissionResource::collection($submissions),
            'Entregas obtenidas exitosamente',
            200
        );
    }
 
    public function store(SubmissionRequest $request)
    {
        $assignment = Assignment::with('group')->find($request->assignment_id);
        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada', 404);
        }
        if (!$assignment->isActive()) {
            return $this->errorResponse('Esta tarea ya no acepta entregas', 422);
        }
        $submissionDate = now();
        $isLate = $submissionDate > $assignment->end_date;
        $submission = Submission::create([
            'assignment_id'   => $request->assignment_id,
            'student_id'      => $request->user()->id,
            'submission_date' => $submissionDate,
            'status'          => $isLate ? 'Entregada tarde' : 'Entregada',
        ]);
        // Guardar archivos adjuntos si vienen
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                File::create([
                    'submission_id' => $submission->id,
                    'user_id'       => $request->user()->id,
                    'context'       => 'student_submission',
                    'file_name'     => $file->getClientOriginalName(),
                    'file_path'     => $file->store('submissions', 'public'),
                    'type'          => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                ]);
            }
        }
        // Notificar al maestro automáticamente
        $student = $request->user();
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
        $submission = Submission::with(['student', 'assignment', 'files'])->find($id);
        if (!$submission) {
            return $this->errorResponse('Entrega no encontrada', 404);
        }
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
        $submission = Submission::with(['student', 'assignment'])->find($id);
        if (!$submission) {
            return $this->errorResponse('Entrega no encontrada', 404);
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
        $submission = Submission::find($id);
        if (!$submission) {
            return $this->errorResponse('Entrega no encontrada', 404);
        }
        $submission->delete();
        return $this->successResponse(null, 'Entrega eliminada exitosamente', 200);
    }
}