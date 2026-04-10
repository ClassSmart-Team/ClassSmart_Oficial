<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();

        $messages = Message::with('user')
            ->whereHas('chat.users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->get();

        return $this->successResponse(
            MessageResource::collection($messages),
            'Mensajes obtenidos exitosamente',
            200
        );
    }

    public function store(MessageRequest $request)
    {
        $user = $request->user();

        $chat = Chat::where('id', $request->chat_id)
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->first();

        if (!$chat) {
            return $this->errorResponse('No tienes acceso a este chat', 403);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        $message->load('user');

        broadcast(new MessageSent($message))->toOthers();

        return $this->successResponse(
            new MessageResource($message),
            'Mensaje enviado exitosamente',
            201
        );
    }

    public function show($id, Request $request)
    {
        $user = $request->user();

        $message = Message::with('user')
            ->where('id', $id)
            ->whereHas('chat.users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->first();

        if (!$message) {
            return $this->errorResponse('Mensaje no encontrado o sin acceso', 404);
        }

        return $this->successResponse(
            new MessageResource($message),
            'Mensaje obtenido exitosamente',
            200
        );
    }

    public function destroy($id, Request $request)
    {
        $message = Message::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$message) {
            return $this->errorResponse('Mensaje no encontrado o sin permisos', 404);
        }

        $message->delete();

        return $this->successResponse(null, 'Mensaje eliminado exitosamente', 200);
    }
}