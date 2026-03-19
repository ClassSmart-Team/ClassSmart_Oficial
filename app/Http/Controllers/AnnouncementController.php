<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\AnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
 
class AnnouncementController extends Controller
{
    use ApiResponse;
 
    public function index()
    {
        $announcements = Announcement::with('group')->get();
 
        return $this->successResponse(
            AnnouncementResource::collection($announcements),
            'Anuncios obtenidos exitosamente',
            200
        );
    }
 
    public function store(AnnouncementRequest $request)
    {
        $data = $request->validated();
        // Procesar archivo adjunto si viene
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $data['attachment_path'] = $file->store('announcements', 'public');
            $data['attachment_name'] = $file->getClientOriginalName();
        }
 
        unset($data['attachment']); // quitar el archivo del array antes de crear
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
        $announcement = Announcement::with('group')->find($id);
        if (!$announcement) {
            return $this->errorResponse('Anuncio no encontrado', 404);
        }
        return $this->successResponse(
            new AnnouncementResource($announcement),
            'Anuncio obtenido exitosamente',
            200
        );
    }
 
    public function update(AnnouncementRequest $request, $id)
    {
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return $this->errorResponse('Anuncio no encontrado', 404);
        }
        $data = $request->validated();
        // Si viene un nuevo archivo, reemplazar el anterior
        if ($request->hasFile('attachment')) {
            // Eliminar archivo anterior si existe
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
        $announcement = Announcement::find($id);
        if (!$announcement) {
            return $this->errorResponse('Anuncio no encontrado', 404);
        }
        // Eliminar archivo adjunto si existe
        if ($announcement->attachment_path) {
            Storage::disk('public')->delete($announcement->attachment_path);
        }
        $announcement->delete();
        return $this->successResponse(null, 'Anuncio eliminado exitosamente', 200);
    }
}
 