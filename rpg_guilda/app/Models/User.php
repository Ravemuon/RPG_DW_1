<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ------------------------------------------------------------
    // 🔹 Lista oficial de temas válidos
    // ------------------------------------------------------------
    public const TEMAS = [
        'medieval',
        'fantasia',
        'sobrenatural',
        'steampunk',
        'cyberpunk',
        'apocaliptico',
        'oceano',
        'floresta',
        'deserto'
    ];

    // ------------------------------------------------------------
    // 🔹 Lista de papéis válidos
    // ------------------------------------------------------------
    public const PAPEIS = [
        'jogador',
        'mestre',
        'administrador'
    ];

    // ------------------------------------------------------------
    // 🔹 Campos preenchíveis
    // ------------------------------------------------------------
    protected $fillable = [
        'nome',
        'username',
        'email',
        'password',
        'biografia',
        'avatar',
        'banner',
        'tema',
        'papel'
    ];

    // ------------------------------------------------------------
    // 🔹 Campos ocultos
    // ------------------------------------------------------------
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ------------------------------------------------------------
    // 🔹 Casts
    // ------------------------------------------------------------
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ------------------------------------------------------------
    // 🔹 Atributos adicionais
    // ------------------------------------------------------------
    protected $appends = ['avatar_url', 'banner_url'];

    // ------------------------------------------------------------
    // 🔹 Setters com validação
    // ------------------------------------------------------------
    public function setTemaAttribute($value): void
    {
        $this->attributes['tema'] = in_array($value, self::TEMAS) ? $value : 'medieval';
    }

    public function setPapelAttribute($value): void
    {
        $this->attributes['papel'] = in_array($value, self::PAPEIS) ? $value : 'jogador';
    }

    // ------------------------------------------------------------
    // 🔹 Relacionamentos
    // ------------------------------------------------------------
    public function personagens()
    {
        return $this->hasMany(Personagem::class, 'user_id');
    }
    
    public function campanhas()
    {
        // Relação direta com as campanhas que o usuário participa
        return $this->belongsToMany(Campanha::class, 'campanha_usuario', 'user_id', 'campanha_id')
                    ->withTimestamps(); // timestamps da pivot, se precisar
    }

    public function notificacoes()
    {
        return $this->hasMany(Notificacao::class, 'usuario_id');
    }

    public function amizadesEnviadas()
    {
        return $this->hasMany(Amizade::class, 'user_id');
    }

    public function amizadesRecebidas()
    {
        return $this->hasMany(Amizade::class, 'friend_id');
    }

    public function amigos()
    {
        return $this->belongsToMany(User::class, 'amizades', 'user_id', 'friend_id')
                    ->wherePivot('status', 'aceita')
                    ->withTimestamps();
    }

    public function amigosRecebidos()
    {
        return $this->belongsToMany(User::class, 'amizades', 'friend_id', 'user_id')
                    ->wherePivot('status', 'aceita')
                    ->withTimestamps();
    }

    public function todosAmigos()
    {
        return $this->amigos->merge($this->amigosRecebidos);
    }

    public function arquivos()
    {
        return $this->hasMany(\App\Models\Arquivo::class, 'usuario_id');
    }

    // ------------------------------------------------------------
    // 🔹 Atributos dinâmicos
    // ------------------------------------------------------------
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar ? asset($this->avatar) : asset('/imagens/default/avatar.png');
    }

    public function getBannerUrlAttribute(): string
    {
        return $this->banner ? asset($this->banner) : asset('/imagens/default/banner.png');
    }

    // ------------------------------------------------------------
    // 🔹 Utilitários
    // ------------------------------------------------------------
    public static function validThemes(): array
    {
        return self::TEMAS;
    }

    public static function validPapeis(): array
    {
        return self::PAPEIS;
    }

    public function canEdit(User $user): bool
    {
        return $this->id === $user->id || $this->papel === 'administrador';
    }
}
