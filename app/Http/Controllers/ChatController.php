<?php
 
namespace App\Http\Controllers;
 
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
 
class ChatController extends Controller
{
    use ApiResponse;

    public function availableUsers(Request $request)
    {
        $users = User::query()
            ->where('id', '!=', $request->user()->id)
            ->whereIn('role_id', [2, 3])
            ->where('active', true)->with("role")
            ->select('id', 'name', 'lastname', 'email', 'role_id')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get();

        return $this->successResponse(
            UserResource::collection($users),
            'Usuarios disponibles para chat obtenidos exitosamente',
            200
        );
    }
    public function index(Request $request)
    {
        $chats = $request->user()
            ->chats()
            ->with('users')
            ->withCount(['users', 'messages'])
            ->get();

        return $this->successResponse(
            ChatResource::collection($chats),
            'Chats obtenidos exitosamente',
            200
        );
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'name'       => ['nullable', 'string', 'max:255'],
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $authUser = $request->user();

        $userIds = array_values(array_unique(array_merge(
            [$authUser->id],
            $request->user_ids
        )));

        if (count($userIds) < 2) {
            return $this->errorResponse(
                'Debes seleccionar al menos otro usuario para crear un chat',
                422
            );
        }

        $chatName = $request->filled('name') ? trim($request->name) : null;
        $isPrivate = $chatName === null && count($userIds) === 2;

        if ($isPrivate) {
            $existingChat = Chat::with('users')
                ->withCount(['users', 'messages'])
                ->whereNull('name')
                ->whereHas('users', function ($query) use ($userIds) {
                    $query->whereIn('users.id', $userIds);
                }, '=', 2)
                ->get()
                ->first(function ($chat) use ($userIds) {
                    $chatUserIds = $chat->users->pluck('id')->sort()->values()->toArray();
                    $incomingUserIds = collect($userIds)->sort()->values()->toArray();

                    return $chatUserIds === $incomingUserIds;
                });

            if ($existingChat) {
                return $this->successResponse(
                    new ChatResource($existingChat),
                    'El chat privado ya existía',
                    200
                );
            }
        }

        $chat = Chat::create([
            'name' => $chatName,
        ]);

        $chat->users()->attach($userIds);
        $chat->load('users');
        $chat->loadCount(['users', 'messages']);

        return $this->successResponse(
            new ChatResource($chat),
            'Chat creado exitosamente',
            201
        );
    }
 
    public function show($id, Request $request)
    {
        $chat = Chat::with(['users', 'messages.user'])
            ->withCount(['users', 'messages'])
            ->where('id', $id)
            ->whereHas('users', function ($query) use ($request) {
                $query->where('users.id', $request->user()->id);
            })
            ->first();

        if (!$chat) {
            return $this->errorResponse('Chat no encontrado o sin acceso', 404);
        }

        return $this->successResponse(
            new ChatResource($chat),
            'Chat obtenido exitosamente',
            200
        );
    }
 
    public function destroy($id, Request $request)
    {
        $user = $request->user();

        $chat = Chat::with('users')
            ->where('id', $id)
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->first();

        if (!$chat) {
            return $this->errorResponse('Chat no encontrado o sin acceso', 404);
        }

        if ($chat->isPrivate()) {
            $chat->delete();

            return $this->successResponse(
                null,
                'Chat privado eliminado exitosamente',
                200
            );
        }

        $chat->users()->detach($user->id);

        if (!$chat->users()->exists()) {
            $chat->delete();

            return $this->successResponse(
                null,
                'Saliste del chat y como ya no quedaban participantes, el chat fue eliminado',
                200
            );
        }

        return $this->successResponse(
            null,
            'Saliste del chat exitosamente',
            200
        );
    }
}