<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\Group;
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

        if ($user && $user->isStudent()) {
            return $query->whereHas('group.students', function ($studentsQuery) use ($user) {
                $studentsQuery
                    ->where('users.id', $user->id)
                    ->where('student_groups.active', true);
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

        unset($data['attachment']);

        $announcement = Announcement::create($data);
        $announcement->load('group');

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
}