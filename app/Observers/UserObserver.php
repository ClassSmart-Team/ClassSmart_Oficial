<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AuditLogger;

class UserObserver
{
    public function created(User $user): void
    {
        $actor = auth()->user();

        if ($actor && $actor->id !== $user->id) {
            AuditLogger::record(
                'users.created',
                "Usuario {$user->email} creado por {$actor->email}",
                $user,
                $actor->id
            );

            return;
        }

        AuditLogger::record(
            'auth.registered',
            "Nueva cuenta registrada: {$user->email}",
            $user,
            $actor?->id
        );
    }

    public function updated(User $user): void
    {
        if (!$user->wasChanged()) {
            return;
        }

        $action = 'users.updated';
        $description = "Usuario {$user->email} actualizado";

        if ($user->wasChanged('active') && $user->active === false) {
            $action = 'users.deactivated';
            $description = "Usuario {$user->email} desactivado";
        }

        AuditLogger::record(
            $action,
            $description,
            $user
        );
    }
}
