<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campanha;
use Illuminate\Auth\Access\Response;

class CampanhaPolicy
{
    public function view(User $user, Campanha $campanha): bool
    {
        if ($campanha->criador_id === $user->id) {
            return true;
        }
        return $campanha->jogadores()
                        ->where('user_id', $user->id)
                        ->wherePivot('status', 'ativo')
                        ->exists();
    }
    public function update(User $user, Campanha $campanha): bool
    {
        // Só o criador pode editar (regra mantida)
        return $campanha->criador_id === $user->id;
    }

    public function delete(User $user, Campanha $campanha): bool
    {
        // Só o criador pode deletar (regra mantida)
        return $campanha->criador_id === $user->id;
    }
}
