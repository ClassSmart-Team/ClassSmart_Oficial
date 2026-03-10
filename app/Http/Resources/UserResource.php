<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'name' => $this->name,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'cellphone' => $this->cellphone,
            'active' => $this->active,
            'role_id' => $this->role_id,
        ];
    }
}
