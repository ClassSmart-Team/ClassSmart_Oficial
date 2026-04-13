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

    public function download($id)
    {
        $user = auth()->user();
        $file = File::with(['assignment', 'submission'])->findOrFail($id);

        if ($file->context === 'assignment_material') {
            return Storage::disk('public')->download($file->file_path, $file->file_name);
        }

        if ($file->context === 'student_submission') {
            if (in_array($user->role_id, [1, 2])) {
                return Storage::disk('public')->download($file->file_path, $file->file_name);
            }

            // El Alumno solo puede bajar SU propia entrega
            if ($user->role_id === 3 && $file->submission->student_id === $user->id) {
                return Storage::disk('public')->download($file->file_path, $file->file_name);
            }

            if ($user->role_id === 4) {
                $isHisChild = $user->children()->where('users.id', $file->submission->student_id)->exists();
                if ($isHisChild) {
                    return Storage::disk('public')->download($file->file_path, $file->file_name);
                }
            }
        }

        return $this->errorResponse('No tienes permiso para acceder a este archivo.', 403);
    }

    public function view($id)
    {
        $user = auth()->user();
        $file = File::findOrFail($id);

        if ($file->context === 'student_submission') {
            if ($user->role_id === 3 && $file->user_id !== $user->id) {
                return response()->json(['message' => 'No tienes permiso para ver esta entrega'], 403);
            }

            if ($user->role_id === 4) {
                $isHisChild = $user->children()->where('users.id', $file->user_id)->exists();
                if (!$isHisChild) {
                    return response()->json(['message' => 'Este archivo no pertenece a tu hijo'], 403);
                }
            }
        }

        $path = storage_path('app/public/' . $file->file_path);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Archivo físico no encontrado'], 404);
        }

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . $file->file_name . '"'
        ]);
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}
