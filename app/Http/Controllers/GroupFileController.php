<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\GroupFileRequest;
use App\Http\Resources\GroupFileResource;
use App\Models\GroupFile;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;
 
class GroupFileController extends Controller
{
    use ApiResponse;
 
    public function index()
    {
        $files = GroupFile::with(['group', 'uploadedBy'])->get();
        return $this->successResponse(
            GroupFileResource::collection($files),
            'Archivos obtenidos exitosamente',
            200
        );
    }
 
    public function store(GroupFileRequest $request)
    {
        $uploadedFile = $request->file('file');
        $groupFile = GroupFile::create([
            'group_id'    => $request->group_id,
            'uploaded_by' => $request->user()->id,
            'file_name'   => $uploadedFile->getClientOriginalName(),
            'file_path'   => $uploadedFile->store('group-files', 'public'),
            'type'        => $uploadedFile->getClientMimeType(),
            'size'        => $uploadedFile->getSize(),
            'description' => $request->description,
        ]);
        $groupFile->load(['group', 'uploadedBy']);
        return $this->successResponse(
            new GroupFileResource($groupFile),
            'Archivo subido exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $groupFile = GroupFile::with(['group', 'uploadedBy'])->find($id);
        if (!$groupFile) {
            return $this->errorResponse('Archivo no encontrado', 404);
        }
        return $this->successResponse(
            new GroupFileResource($groupFile),
            'Archivo obtenido exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $groupFile = GroupFile::find($id);
        if (!$groupFile) {
            return $this->errorResponse('Archivo no encontrado', 404);
        }
        Storage::disk('public')->delete($groupFile->file_path);
        $groupFile->delete();
        return $this->successResponse(null, 'Archivo eliminado exitosamente', 200);
    }
}