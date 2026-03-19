<?php
 
namespace App\Http\Controllers;
 
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
 
class ChatController extends Controller
{
    use ApiResponse;
 
    public function index(Request $request)
    {
        // Solo devuelve los chats en los que participa el usuario autenticado
        $chats = $request->user()->chats()->with('users')->withCount('messages')->get();
        return $this->successResponse(
            ChatResource::collection($chats),
            'Chats obtenidos exitosamente',
            200
        );
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['nullable', 'string', 'max:255'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);
        $chat = Chat::create(['name' => $request->name]);
        // Agregar al usuario autenticado + los usuarios seleccionados
        $userIds = array_unique(array_merge(
            [$request->user()->id],
            $request->user_ids
        ));
        $chat->users()->attach($userIds);
        $chat->load('users');
 
        return $this->successResponse(
            new ChatResource($chat),
            'Chat creado exitosamente',
            201
        );
    }
 
    public function show($id)
    {
        $chat = Chat::with(['users', 'messages.user'])->find($id);
 
        if (!$chat) {
            return $this->errorResponse('Chat no encontrado', 404);
        }
 
        return $this->successResponse(
            new ChatResource($chat),
            'Chat obtenido exitosamente',
            200
        );
    }
 
    public function destroy($id)
    {
        $chat = Chat::find($id);
 
        if (!$chat) {
            return $this->errorResponse('Chat no encontrado', 404);
        }
 
        $chat->delete();
 
        return $this->successResponse(null, 'Chat eliminado exitosamente', 200);
    }
}