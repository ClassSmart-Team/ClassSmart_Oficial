<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roleIds)
    {
        $user = Auth::user();
 
        // Convertir todos los roleIds a enteros para comparar correctamente
        $roleIds = array_map('intval', $roleIds);
 
        if (!$user || !in_array($user->role_id, $roleIds)) {
            return response()->json([
                'status'  => false,
                'message' => 'Acceso denegado',
                'data'    => null,
                'error'   => 'No tienes permisos para realizar esta acción',
            ], 403);
        }
 
        return $next($request);
    }
}