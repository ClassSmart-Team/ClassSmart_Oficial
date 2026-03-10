<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Traits\ApiResponse;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    use ApiResponse;
    public function login (LoginRequest $request){
        $data = $request->validated();
        $user = User::where("email", $data["email"])->first();

        if (!$user||!Hash::check($data["password"], $user->password)){
            return $this-> errorResponse(
                "Contraseña o Usuario Incorrecto", 401
            );
        }
        $token = $user->createToken("token")->plainTextToken;

        return $this->successResponse(
            $token, "Aqui esta tu Token chaval",
            200
        );
    }
}
