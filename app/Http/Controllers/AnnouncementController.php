<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\Group;
use App\Notifications\AnnouncementNotification;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    use ApiResponse;

    private function visibleAnnouncementsQuery($user): Builder
    {
        $query = Announcement::query();

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

    private function findAccessibleAnnouncement(int|string $id): ?Announcement
    {
        return $this->visibleAnnouncementsQuery(request()->user())->find($id);
    }

    private function userCanUseGroup(Request $request, int $groupId): bool
    {
        $user = $request->user();

        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return Group::whereKey($groupId)->exists();
        }

        if ($user->isTeacher()) {
            return Group::whereKey($groupId)
                ->where('owner', $user->id)
                ->exists();
        }

        return false;
    }

    public function index(Request $request)
    {
        $announcements = $this->visibleAnnouncementsQuery($request->user())
            ->with('group')
            ->latest()
            ->get();

        return $this->successResponse(
            AnnouncementResource::collection($announcements),
            'Anuncios obtenidos exitosamente',
            200
        );
    }

    public function store(AnnouncementRequest $request)
    {
        $data = $request->validated();

        if (!$this->userCanUseGroup($request, $data['group_id'])) {
            return $this->errorResponse('No tienes permisos para publicar anuncios en ese grupo.', 403);
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $data['attachment_path'] = $file->store('announcements', 'public');
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        unset($data['attachment']); // quitar el archivo del array antes de crear
        $announcement = Announcement::create($data);
        $announcement->load('group');

        $students = $announcement->group->students()->with('parents')->get();

        foreach ($students as $student) {
            // Notificar al alumno
            $student->notify(new AnnouncementNotification($announcement, $student));

            foreach ($student->parents as $parent) {
                $parent->notify(new AnnouncementNotification($announcement, $student));
            }
        }

        return $this->successResponse(
            new AnnouncementResource($announcement),
            'Anuncio creado exitosamente',
            201
        );
    }

    public function show($id)
    {
        $announcement = $this->findAccessibleAnnouncement($id);

        if (!$announcement) {
            return $this->errorResponse('Anuncio no encontrado o sin permisos para verlo', 404);
        }

        $announcement->load('group');

        return $this->successResponse(
            new AnnouncementResource($announcement),
            'Anuncio obtenido exitosamente',
            200
        );
    }

    public function update(AnnouncementRequest $request, $id)
    {
        $announcement = $this->findAccessibleAnnouncement($id);

        if (!$announcement) {
            return $this->errorResponse('Anuncio no encontrado o sin permisos para editarlo', 404);
        }

        $data = $request->validated();

        if (array_key_exists('group_id', $data) && !$this->userCanUseGroup($request, $data['group_id'])) {
            return $this->errorResponse('No tienes permisos para mover este anuncio a ese grupo.', 403);
        }

        if ($request->hasFile('attachment')) {
            if ($announcement->attachment_path) {
                Storage::disk('public')->delete($announcement->attachment_path);
            }

            $file = $request->file('attachment');
            $data['attachment_path'] = $file->store('announcements', 'public');
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        unset($data['attachment']);

        $announcement->update($data);
        $announcement->load('group');

        return $this->successResponse(
            new AnnouncementResource($announcement),
            'Anuncio actualizado exitosamente',
            200
        );
    }

    public function destroy($id)
    {
        $announcement = $this->findAccessibleAnnouncement($id);

        if (!$announcement) {
            return $this->errorResponse('Anuncio no encontrado o sin permisos para eliminarlo', 404);
        }

        if ($announcement->attachment_path) {
            Storage::disk('public')->delete($announcement->attachment_path);
        }

        $announcement->delete();

        return $this->successResponse(null, 'Anuncio eliminado exitosamente', 200);
    }

    /* PARENT */
    public function getParentAnnouncements(Request $request)
    {
        $user = $request->user();

        $groupIds = Group::whereHas('students', function ($query) use ($user) {
            $query->whereIn('users.id', $user->children()->pluck('id'));
        })->pluck('id'); //lista de los id de todos los grupos de los hijos

        $announcements = Announcement::with('group.ownerUser')
            ->whereIn('group_id', $groupIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse(
            AnnouncementResource::collection($announcements),
            'Anuncios obtenidos correctamente'
        );
    }

    public function getParentAnnouncementDetail(Request $request, $id)
    {
        $user = $request->user();

        $announcement = Announcement::with(['group'])
            ->where('id', $id)
            ->whereHas('group.students', function ($query) use ($user) {
                $query->whereIn('users.id', $user->children()->pluck('id'));
            })
            ->first();

        if (!$announcement) {
            return $this->errorResponse('Anuncio no encontrado o no tienes permiso para verlo', 404);
        }

        return $this->successResponse(
            new AnnouncementResource($announcement),
            'Detalle del anuncio obtenido'
        );
    }
}
