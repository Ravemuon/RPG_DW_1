<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campanha;

class CampanhaPolicy
{
    public function view(User $user, Campanha $campanha)
    {
        // Pode ver se participa ou criou
        return $campanha->criador_id === $user->id
            || $campanha->participantes->contains($user->id);
    }

    public function update(User $user, Campanha $campanha)
    {
        // Só o criador pode editar
        return $campanha->criador_id === $user->id;
    }

    public function delete(User $user, Campanha $campanha)
    {
        // Só o criador pode deletar
        return $campanha->criador_id === $user->id;
    }
}
