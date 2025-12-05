<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campanha;

class CampanhaPolicy
{
    // Define se o usuário pode visualizar a campanha
    public function view(User $user, Campanha $campanha): bool
    {
        // O criador sempre pode ver
        if ($campanha->criador_id === $user->id) {
            return true;
        }

        // Jogadores ativos também podem ver
        return $campanha->jogadores()
                        ->where('user_id', $user->id)
                        ->wherePivot('status', 'ativo')
                        ->exists();
    }

    // Só o criador pode atualizar a campanha
    public function update(User $user, Campanha $campanha): bool
    {
        return $campanha->criador_id === $user->id;
    }

    // Só o criador pode deletar a campanha
    public function delete(User $user, Campanha $campanha): bool
    {
        return $campanha->criador_id === $user->id;
    }
}
