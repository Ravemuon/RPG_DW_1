<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campanha;

class CampanhaPolicy
{
    /**
     * Define se o usuário pode visualizar a campanha.
     * - O criador sempre pode ver.
     * - Jogadores ativos também podem ver.
     */
    public function view(User $user, Campanha $campanha): bool
    {
        // Criador da campanha sempre pode ver
        if ($campanha->criador_id === $user->id) {
            return true;
        }

        // Jogadores ativos da campanha podem ver
        return $campanha->jogadores()
                        ->where('user_id', $user->id)
                        ->wherePivot('status', 'ativo')
                        ->exists();
    }

    /**
     * Define se o usuário pode atualizar a campanha.
     * Apenas o criador pode atualizar.
     */
    public function update(User $user, Campanha $campanha): bool
    {
        return $campanha->criador_id === $user->id;
    }

    /**
     * Define se o usuário pode deletar a campanha.
     * Apenas o criador pode deletar.
     */
    public function delete(User $user, Campanha $campanha): bool
    {
        return $campanha->criador_id === $user->id;
    }

    /**
     * Define se o usuário pode criar sessões dentro da campanha.
     * Apenas o criador pode criar sessões.
     */
    public function createSessao(User $user, Campanha $campanha): bool
    {
        return $campanha->criador_id === $user->id;
    }

    /**
     * Define se o usuário pode gerenciar personagens na campanha.
     * Apenas o criador pode adicionar ou remover personagens.
     */
    public function managePersonagens(User $user, Campanha $campanha): bool
    {
        return $campanha->criador_id === $user->id;
    }

    public function criarPersonagem(User $user, Campanha $campanha): bool
{
    // Criador pode sempre criar
    if ($campanha->criador_id === $user->id) {
        return true;
    }
    
    // Jogadores ativos podem criar
    if ($campanha->jogadores()
        ->where('user_id', $user->id)
        ->whereIn('campanha_usuario.status', ['ativo', 'mestre'])
        ->exists()) {
        return true;
    }
    
    // Campanhas públicas permitem criação
    if (!$campanha->privada) {
        return true;
    }
    
    return false;
}
}
