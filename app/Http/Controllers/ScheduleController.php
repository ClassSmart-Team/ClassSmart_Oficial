<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\ScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Traits\ApiResponse;
 
class ScheduleController extends Controller
{
    use ApiResponse;
 
    public function index()
    {
        $schedules = Schedule::with('group')->get();
        return $this->successResponse(
            ScheduleResource::collection($schedules),
            'Horarios obtenidos exitosamente',
            200
        );
    }
 
    public function store(ScheduleRequest $request)
    {
        $schedule = Schedule::create($request->validated());
        $schedule->load('group');
        return $this->successResponse(
            new ScheduleResource($schedule),
            'Horario creado exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $schedule = Schedule::with('group')->find($id);
 
        if (!$schedule) {
            return $this->errorResponse('Horario no encontrado', 404);
        }
        return $this->successResponse(
            new ScheduleResource($schedule),
            'Horario obtenido exitosamente',
            200
        );
    }
 
    public function update(ScheduleRequest $request, $id)
    {
        $schedule = Schedule::find($id);
 
        if (!$schedule) {
            return $this->errorResponse('Horario no encontrado', 404);
        }
        $schedule->update($request->validated());
        $schedule->load('group');
        return $this->successResponse(
            new ScheduleResource($schedule),
            'Horario actualizado exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            return $this->errorResponse('Horario no encontrado', 404);
        }
        $schedule->delete();
        return $this->successResponse(null, 'Horario eliminado exitosamente', 200);
    }
}