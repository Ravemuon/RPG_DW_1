<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'email',
        'password',
        'tema',
        'tipo', // jogador, mestre ou admin
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relacionamento com personagens
    public function personagens()
    {
        return $this->hasMany(Personagem::class, 'user_id');
    }

    // Relacionamento com campanhas (se houver)
    public function campanhas()
    {
        return $this->belongsToMany(Campanha::class, 'campanha_usuario') // tabela pivot
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // Relacionamento com notificações
    public function notificacoes()
    {
        return $this->hasMany(Notificacao::class, 'usuario_id');
    }

    public function perfil()
    {
        return $this->hasOne(\App\Models\Arquivo::class, 'usuario_id', 'id')->latestOfMany('id_arquivo');
    }
    public function getAvatarUrlAttribute()
    {
        if ($this->perfil?->caminho) {
            return Storage::url($this->perfil->caminho);
        }
        return asset('images/default-avatar.png');
    }
    public function banner()
    {
        return $this->hasOne(Arquivo::class, 'usuario_id')->where('tipo', 'banner');
    }



}
