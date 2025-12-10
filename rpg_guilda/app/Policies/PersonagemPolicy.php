<?php

namespace App\Policies;

use App\Models\Personagem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonagemPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode ver o personagem
     */
    public function view(User $user, Personagem $personagem): bool
    {
        // Dono do personagem
        if ($personagem->user_id === $user->id) {
            return true;
        }

        // Mestre da campanha
        if ($personagem->campanha && $personagem->campanha->criador_id === $user->id) {
            return true;
        }

        // Participante da campanha (se a campanha permitir)
        if ($personagem->campanha && !$personagem->campanha->privada) {
            return true;
        }

        return false;
    }

    /**
     * Determina se o usuário pode criar personagem na campanha
     */
    public function create(User $user, $campanhaId): bool
    {
        // Verificar se usuário pode participar da campanha
        // Implemente a lógica conforme seu sistema de participação
        return true;
    }

    /**
     * Determina se o usuário pode atualizar o personagem
     */
    public function update(User $user, Personagem $personagem): bool
    {
        // Apenas dono pode editar
        return $personagem->user_id === $user->id;
    }

    /**
     * Determina se o usuário pode deletar o personagem
     */
    public function delete(User $user, Personagem $personagem): bool
    {
        // Apenas dono pode deletar
        return $personagem->user_id === $user->id;
    }

    /**
     * Determina se o usuário pode restaurar o personagem
     */
    public function restore(User $user, Personagem $personagem): bool
    {
        // Apenas dono pode restaurar
        return $personagem->user_id === $user->id;
    }
}