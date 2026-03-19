<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\AssignmentRequest;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\File;
use App\Models\Notification;
use App\Traits\ApiResponse;
 
class AssignmentController extends Controller
{
    use ApiResponse;
 
    public function index()
    {
        $assignments = Assignment::with(['group', 'unit'])
            ->withCount('submissions')
            ->get();
        return $this->successResponse(
            AssignmentResource::collection($assignments),
            'Tareas obtenidas exitosamente',
            200
        );
    }
 
    public function store(AssignmentRequest $request)
    {
        $data = $request->validated();
        unset($data['files']);
        $assignment = Assignment::create($data);
 
        // Guardar archivos adjuntos del maestro
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                File::create([
                    'assignment_id' => $assignment->id,
                    'user_id'       => $request->user()->id,
                    'context'       => 'assignment_material',
                    'file_name'     => $file->getClientOriginalName(),
                    'file_path'     => $file->store('assignments', 'public'),
                    'type'          => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                ]);
            }
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
        $assignment = Assignment::with(['group', 'unit', 'files', 'submissions'])
            ->withCount('submissions')
            ->find($id);
 
        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada', 404);
        }
 
        return $this->successResponse(
            new AssignmentResource($assignment),
            'Tarea obtenida exitosamente',
            200
        );
    }
 
    public function update(AssignmentRequest $request, $id)
    {
        $assignment = Assignment::find($id);
 
        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada', 404);
        }
        $data = $request->validated();
        unset($data['files']);
        $assignment->update($data);
 
        // Agregar nuevos archivos si vienen
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                File::create([
                    'assignment_id' => $assignment->id,
                    'user_id'       => $request->user()->id,
                    'context'       => 'assignment_material',
                    'file_name'     => $file->getClientOriginalName(),
                    'file_path'     => $file->store('assignments', 'public'),
                    'type'          => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                ]);
            }
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
        $assignment = Assignment::find($id);
 
        if (!$assignment) {
            return $this->errorResponse('Tarea no encontrada', 404);
        }
        $assignment->delete();
        return $this->successResponse(null, 'Tarea eliminada exitosamente', 200);
    }
}