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
                [
                    'created_user_id' => $user->id,
                    'created_role_id' => $user->role_id,
                ],
                $actor->id
            );

            return;
        }

        AuditLogger::record(
            'auth.registered',
            "Nueva cuenta registrada: {$user->email}",
            $user,
            [
                'registered_user_id' => $user->id,
                'role_id' => $user->role_id,
            ],
            $actor?->id
        );
    }

    public function updated(User $user): void
    {
        if (!$user->wasChanged()) {
            return;
        }

        $changes = $user->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $oldValues = [];
        foreach (array_keys($changes) as $field) {
            $oldValues[$field] = $user->getOriginal($field);
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
            $user,
            [
                'old' => $oldValues,
                'new' => $changes,
            ]
        );
    }
}
