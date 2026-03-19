<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Traits\ApiResponse;
 
class MessageController extends Controller
{
    use ApiResponse;
 
    public function index()
    {
        $messages = Message::with(['user', 'chat'])->get();
        return $this->successResponse(
            MessageResource::collection($messages),
            'Mensajes obtenidos exitosamente',
            200
        );
    }
 
    public function store(MessageRequest $request)
    {
        $message = Message::create([
            'chat_id' => $request->chat_id,
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);
        $message->load(['user', 'chat']);
        return $this->successResponse(
            new MessageResource($message),
            'Mensaje enviado exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $message = Message::with(['user', 'chat'])->find($id);
        if (!$message) {
            return $this->errorResponse('Mensaje no encontrado', 404);
        }
        return $this->successResponse(
            new MessageResource($message),
            'Mensaje obtenido exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $message = Message::find($id);
        if (!$message) {
            return $this->errorResponse('Mensaje no encontrado', 404);
        }
        $message->delete();
        return $this->successResponse(null, 'Mensaje eliminado exitosamente', 200);
    }
}