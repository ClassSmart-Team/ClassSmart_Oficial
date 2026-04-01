<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\UnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Group;
use App\Models\Unit;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
 
class UnitController extends Controller
{
    use ApiResponse;

    private function visibleUnitsQuery($user): Builder
    {
        $query = Unit::query();

        if ($user && $user->isAdmin()) {
            return $query;
        }

        if ($user && $user->isTeacher()) {
            return $query->whereHas('group', function ($groupQuery) use ($user) {
                $groupQuery->where('owner', $user->id);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    private function findAccessibleUnit(int|string $id): ?Unit
    {
        return $this->visibleUnitsQuery(request()->user())->find($id);
    }
 
    public function index(Request $request)
    {
        $query = $this->visibleUnitsQuery($request->user())
            ->with('group')
            ->withCount('assignments')
            ->orderBy('order');

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->integer('group_id'));
        }

        $units = $query->get();

        return $this->successResponse(
            UnitResource::collection($units),
            'Unidades obtenidas exitosamente',
            200
        );
    }
 
    public function store(UnitRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        if (!$user || (!$user->isAdmin() && !$user->isTeacher())) {
            return $this->errorResponse('No tienes permisos para crear unidades', 403);
        }

        if ($user && $user->isTeacher()) {
            $ownsGroup = Group::query()
                ->whereKey($data['group_id'])
                ->where('owner', $user->id)
                ->exists();

            if (!$ownsGroup) {
                return $this->errorResponse('No puedes crear unidades en grupos que no te pertenecen', 403);
            }
        }

        $orderAlreadyUsed = Unit::query()
            ->where('group_id', $data['group_id'])
            ->where('order', $data['order'])
            ->exists();

        if ($orderAlreadyUsed) {
            return $this->errorResponse('Ya existe una unidad con ese orden en el grupo seleccionado', 422);
        }

        $unit = Unit::create($data);
        $unit->load('group');
        return $this->successResponse(
            new UnitResource($unit),
            'Unidad creada exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $unit = $this->findAccessibleUnit($id);

        if (!$unit) {
            return $this->errorResponse('Unidad no encontrada o sin permisos para verla', 404);
        }

        $unit->load(['group', 'assignments', 'gradeRecords']);
        $unit->loadCount('assignments');

        return $this->successResponse(
            new UnitResource($unit),
            'Unidad obtenida exitosamente',
            200
        );
    }
 
    public function update(UnitRequest $request, $id)
    {
        $unit = $this->findAccessibleUnit($id);

        if (!$unit) {
            return $this->errorResponse('Unidad no encontrada o sin permisos para editarla', 404);
        }

        $data = $request->validated();
        $user = $request->user();

        if ($user && $user->isTeacher()) {
            $ownsTargetGroup = Group::query()
                ->whereKey($data['group_id'])
                ->where('owner', $user->id)
                ->exists();

            if (!$ownsTargetGroup) {
                return $this->errorResponse('No puedes mover unidades a grupos que no te pertenecen', 403);
            }
        }

        $unit->update($data);
        $unit->load('group');
        return $this->successResponse(
            new UnitResource($unit),
            'Unidad actualizada exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $unit = $this->findAccessibleUnit($id);

        if (!$unit) {
            return $this->errorResponse('Unidad no encontrada o sin permisos para eliminarla', 404);
        }

        $unit->delete();
        return $this->successResponse(null, 'Unidad eliminada exitosamente', 200);
    }
}