<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\GradeRecordRequest;
use App\Http\Resources\GradeRecordResource;
use App\Models\GradeRecord;
use App\Models\Notification;
use App\Traits\ApiResponse;
 
class GradeRecordController extends Controller
{
    use ApiResponse;
 
    public function index()
    {
        $gradeRecords = GradeRecord::with(['student', 'group', 'unit'])->get();
        return $this->successResponse(
            GradeRecordResource::collection($gradeRecords),
            'Calificaciones obtenidas exitosamente',
            200
        );
    }
 
    public function store(GradeRecordRequest $request)
    {
        $data = $request->validated();
        $gradeRecord = GradeRecord::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'group_id'   => $data['group_id'],
                'unit_id'    => $data['unit_id'],
            ],
            ['grade' => $data['grade']]
        );
        $gradeRecord->load(['student', 'group', 'unit']);
        // Notificar al padre automáticamente
        $student = $gradeRecord->student;
        Notification::notifyParentsOf($student, [
            'created_by' => $request->user()->id,
            'title'      => 'Calificación registrada',
            'message'    => "Se registró la calificación final de {$student->name} en {$gradeRecord->unit->name}: {$gradeRecord->grade}",
            'type'       => 'Individual',
            'related_group' => $data['group_id'],
        ]);

        return $this->successResponse(
            new GradeRecordResource($gradeRecord),
            'Calificación registrada exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $gradeRecord = GradeRecord::with(['student', 'group', 'unit'])->find($id);
        if (!$gradeRecord) {
            return $this->errorResponse('Calificación no encontrada', 404);
        }
        return $this->successResponse(
            new GradeRecordResource($gradeRecord),
            'Calificación obtenida exitosamente',
            200
        );
    }
 
    public function update(GradeRecordRequest $request, $id)
    {
        $gradeRecord = GradeRecord::find($id);
        if (!$gradeRecord) {
            return $this->errorResponse('Calificación no encontrada', 404);
        }
        $gradeRecord->update($request->validated());
        $gradeRecord->load(['student', 'group', 'unit']);
        return $this->successResponse(
            new GradeRecordResource($gradeRecord),
            'Calificación actualizada exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $gradeRecord = GradeRecord::find($id);
        if (!$gradeRecord) {
            return $this->errorResponse('Calificación no encontrada', 404);
        }
        $gradeRecord->delete();
        return $this->successResponse(null, 'Calificación eliminada exitosamente', 200);
    }
}