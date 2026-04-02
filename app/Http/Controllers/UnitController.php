<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Traits\ApiResponse;

class UnitController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $units = Unit::with('group')->withCount('assignments')->get();
        return $this->successResponse(
            UnitResource::collection($units),
            'Unidades obtenidas exitosamente',
            200
        );
    }

    public function store(UnitRequest $request)
    {
        $unit = Unit::create($request->validated());
        $unit->load('group');
        return $this->successResponse(
            new UnitResource($unit),
            'Unidad creada exitosamente',
            201
        );
    }

    public function show($id)
    {
        $unit = Unit::with(['group', 'assignments', 'gradeRecords'])
            ->withCount('assignments')
            ->find($id);
        if (!$unit) {
            return $this->errorResponse('Unidad no encontrada', 404);
        }
        return $this->successResponse(
            new UnitResource($unit),
            'Unidad obtenida exitosamente',
            200
        );
    }

    public function update(UnitRequest $request, $id)
    {
        $unit = Unit::find($id);
        if (!$unit) {
            return $this->errorResponse('Unidad no encontrada', 404);
        }
        $unit->update($request->validated());
        $unit->load('group');
        return $this->successResponse(
            new UnitResource($unit),
            'Unidad actualizada exitosamente',
            200
        );
    }

    public function destroy($id)
    {
        $unit = Unit::find($id);
        if (!$unit) {
            return $this->errorResponse('Unidad no encontrada', 404);
        }
        $unit->delete();
        return $this->successResponse(null, 'Unidad eliminada exitosamente', 200);
    }
}
