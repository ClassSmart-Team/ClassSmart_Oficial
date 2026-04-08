<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditResource;
use App\Models\Audit;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Audit::query()->with('actor')->latest();

        if ($request->filled('action')) {
            $query->where('action', (string) $request->string('action'));
        }

        if ($request->filled('actor_user_id')) {
            $query->where('actor_user_id', $request->integer('actor_user_id'));
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', (string) $request->string('entity_type'));
        }

        if ($request->filled('entity_id')) {
            $query->where('entity_id', $request->integer('entity_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', (string) $request->string('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', (string) $request->string('to'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->string('search');

            $query->where(function ($innerQuery) use ($search) {
                $innerQuery->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('actor', function ($actorQuery) use ($search) {
                        $actorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = max(1, min((int) $request->input('per_page', 20), 100));
        $audits = $query->paginate($perPage);

        return $this->successResponse([
            'items' => AuditResource::collection($audits->items()),
            'pagination' => [
                'current_page' => $audits->currentPage(),
                'last_page' => $audits->lastPage(),
                'per_page' => $audits->perPage(),
                'total' => $audits->total(),
            ],
        ], 'Auditorias obtenidas exitosamente', 200);
    }
}
