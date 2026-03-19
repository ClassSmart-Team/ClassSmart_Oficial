<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\FileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;
 
class FileController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $files = File::with(['user', 'assignment', 'submission'])->get();
 
        return $this->successResponse(
            FileResource::collection($files),
            'Archivos obtenidos exitosamente',
            200
        );
    }

    public function store(FileRequest $request)
    {
        $uploadedFile = $request->file('file');
 
        $file = File::create([
            'submission_id' => $request->submission_id,
            'assignment_id' => $request->assignment_id,
            'user_id'       => $request->user()->id,
            'context'       => $request->context,
            'file_name'     => $uploadedFile->getClientOriginalName(),
            'file_path'     => $uploadedFile->store('files', 'public'),
            'type'          => $uploadedFile->getClientMimeType(),
            'size'          => $uploadedFile->getSize(),
        ]);
        $file->load(['user', 'assignment', 'submission']);
        return $this->successResponse(
            new FileResource($file),
            'Archivo subido exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $file = File::with(['user', 'assignment', 'submission'])->find($id);
        if (!$file) {
            return $this->errorResponse('Archivo no encontrado', 404);
        }
        return $this->successResponse(
            new FileResource($file),
            'Archivo obtenido exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $file = File::find($id);
        if (!$file) {
            return $this->errorResponse('Archivo no encontrado', 404);
        }
        // Eliminar archivo físico del storage
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        return $this->successResponse(null, 'Archivo eliminado exitosamente', 200);
    }
}