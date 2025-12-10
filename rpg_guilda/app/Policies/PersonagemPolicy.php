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
     * Determina se o usuário pode criar personagem
     */
    public function create(User $user): bool
    {
        // Qualquer usuário autenticado pode criar personagem
        // Se quiser restringir, adicione lógica aqui
        return true;
    }

    /**
     * Determina se o usuário pode criar personagem em uma campanha específica
     */
    public function criarPersonagem(User $user, $campanha): bool
    {
        // Se precisar verificar permissões específicas para criar em uma campanha
        // Use este método separado
        
        // Verificar se usuário pode participar da campanha
        if ($campanha->criador_id === $user->id) {
            return true;
        }

        // Verificar se é jogador da campanha
        if ($campanha->jogadores()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Se campanha não é privada, qualquer um pode criar
        if (!$campanha->privada) {
            return true;
        }

        return false;
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