<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Traits\ApiResponse;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    use ApiResponse;

    public function readAll(){
        return response()->json(User::with("role")->get());
    }

     public function create(RegisterRequest $request){
        $data = $request->validated();
        $data["password"] = Hash::make($data["password"]);
        $user = User::create($data);
        return $this->successResponse(new UserResource($user), "Usuario creado exitosamente", 201);
    }

    public function readOne($id){
        return response()->json(User::with("role")->findOrFail($id));
    }

    public function update(Request $request, $id){
        $user = User::findOrFail($id);
        $updateData = $request->only([
            "name",
            "lastname",
            "email",
            "password",
            "cellphone",
            "active",
            "id_role"
        ]);

        if (!empty($updateData["password"])){
            $updateData["password"] = Hash::make($updateData["password"]);
        }
        else{
            unset($updateData["password"]);
        }
        $user->update($updateData);
        return response()->json($user);
    }

    public function delete($id){
        $user = User::findorFail($id);
        $user->active = false;
        $user->save();
        return response()->json(["message" => "Usuario desactivado"]);
    }
}
