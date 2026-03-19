<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\PeriodRequest;
use App\Http\Resources\PeriodResource;
use App\Models\Period;
use App\Traits\ApiResponse;
 
class PeriodController extends Controller
{
    use ApiResponse;
 
    public function index()
    {
        $periods = Period::withCount('groups')->get();
        return $this->successResponse(
            PeriodResource::collection($periods),
            'Periodos obtenidos exitosamente',
            200
        );
    }
 
    public function store(PeriodRequest $request)
    {
        $period = Period::create($request->validated());
        return $this->successResponse(
            new PeriodResource($period),
            'Periodo creado exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $period = Period::with('groups')->withCount('groups')->find($id);
        if (!$period) {
            return $this->errorResponse('Periodo no encontrado', 404);
        }
        return $this->successResponse(
            new PeriodResource($period),
            'Periodo obtenido exitosamente',
            200
        );
    }
 
    public function update(PeriodRequest $request, $id)
    {
        $period = Period::find($id);
        if (!$period) {
            return $this->errorResponse('Periodo no encontrado', 404);
        }
        $period->update($request->validated());
        return $this->successResponse(
            new PeriodResource($period),
            'Periodo actualizado exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $period = Period::find($id);
        if (!$period) {
            return $this->errorResponse('Periodo no encontrado', 404);
        }
        $period->delete();
        return $this->successResponse(null, 'Periodo eliminado exitosamente', 200);
    }
}