<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Campanha;

class SolicitarEntradaCampanha extends Notification
{
    protected $user;
    protected $campanha;

    public function __construct($user, Campanha $campanha)
    {
        $this->user = $user;
        $this->campanha = $campanha;
    }

    public function via($notifiable)
    {
        return ['database'];  // Utilizando o banco para armazenamento das notificações
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'campanha_id' => $this->campanha->id,
            'message' => "{$this->user->nome} solicitou entrar na campanha: {$this->campanha->nome}.",
            'url' => route('campanhas.show', $this->campanha->id),  // Link para a campanha
        ];
    }
}
