<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    // Solo devuelve las notificaciones del usuario autenticado
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->receivedNotifications()
            ->with(['creator', 'group', 'assignment'])
            ->get();

        return $this->successResponse(
            NotificationResource::collection($notifications),
            'Notificaciones obtenidas exitosamente',
            200
        );
    }

    public function store(NotificationRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $notification = Notification::create($data);
        // Si es General, notificar a todos los alumnos del grupo
        if ($data['type'] === 'General' && isset($data['related_group'])) {
            $notification->load('group.students');
            $studentIds = $notification->group->students->pluck('id');
            $notification->recipients()->attach($studentIds, ['read_at' => null]);
        }
        $notification->load(['creator', 'group', 'assignment']);
        return $this->successResponse(
            new NotificationResource($notification),
            'Notificación creada exitosamente',
            201
        );
    }

    public function show($id)
    {
        $notification = Notification::with(['creator', 'group', 'assignment'])->find($id);
        if (!$notification) {
            return $this->errorResponse('Notificación no encontrada', 404);
        }
        return $this->successResponse(
            new NotificationResource($notification),
            'Notificación obtenida exitosamente',
            200
        );
    }

    public function destroy($id)
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return $this->errorResponse('Notificación no encontrada', 404);
        }
        $notification->delete();
        return $this->successResponse(null, 'Notificación eliminada exitosamente', 200);
    }

    // Marcar notificación como leída para el usuario autenticado
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return $this->errorResponse('Notificación no encontrada', 404);
        }
        $notification->markAsReadFor($request->user());
        return $this->successResponse(null, 'Notificación marcada como leída', 200);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        $unreadNotifications = $user->receivedNotifications()
            ->wherePivot('read_at', null)
            ->allRelatedIds();

        if ($unreadNotifications->isEmpty()) {
            return response()->json(['message' => 'No hay notificaciones pendientes'], 200);
        }

        $user->receivedNotifications()->updateExistingPivot($unreadNotifications, [
            'read_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas'], 200);
    }
}
