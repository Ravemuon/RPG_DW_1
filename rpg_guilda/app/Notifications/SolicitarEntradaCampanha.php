<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Models\Campanha;

class SolicitarEntradaCampanha extends Notification
{
    protected $user;     // Usuário que está solicitando
    protected $campanha; // Campanha que receberá a solicitação

    public function __construct($user, Campanha $campanha)
    {
        $this->user = $user;
        $this->campanha = $campanha;
    }

    // Define os canais de notificação (apenas database)
    public function via($notifiable)
    {
        return ['database'];
    }

    // Estrutura dos dados que serão salvos no banco
    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'campanha_id' => $this->campanha->id,
            'message' => "{$this->user->nome} solicitou entrar na campanha: {$this->campanha->nome}.",
            'url' => route('campanhas.show', $this->campanha->id),
        ];
    }
}
