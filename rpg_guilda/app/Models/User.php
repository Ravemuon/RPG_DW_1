<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const TEMAS = [
        'medieval', 'fantasia', 'sobrenatural', 'steampunk', 'cyberpunk', 'apocaliptico', 'oceano', 'floresta', 'deserto'
    ];

    public const PAPEIS = [
        'jogador', 'mestre', 'administrador'
    ];

    protected $fillable = [
        'nome', 'username', 'email', 'password', 'biografia', 'avatar', 'banner', 'tema', 'papel'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    protected $appends = ['avatar_url', 'banner_url'];

    public function setTemaAttribute($value): void
    {
        $this->attributes['tema'] = in_array($value, self::TEMAS) ? $value : 'medieval';
    }

    public function setPapelAttribute($value): void
    {
        $this->attributes['papel'] = in_array($value, self::PAPEIS) ? $value : 'jogador';
    }

    public function personagens()
    {
        return $this->hasMany(Personagem::class, 'user_id');
    }

    public function getIsAdminAttribute()
    {
        return $this->papel === 'administrador';
    }

    public function campanhas()
    {
        return $this->belongsToMany(Campanha::class, 'campanha_usuario', 'user_id', 'campanha_id')->withTimestamps();
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
                    ->withPivot('id')
                    ->withTimestamps();
    }

    public function todosAmigos()
    {
        return $this->belongsToMany(User::class, 'amizades', 'user_id', 'friend_id')
                    ->wherePivot('status', 'aceita')
                    ->withTimestamps();
    }

    public function arquivos()
    {
        return $this->hasMany(\App\Models\Arquivo::class, 'usuario_id');
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar && file_exists(storage_path('app/public/' . $this->avatar))
            ? asset('storage/' . $this->avatar)
            : asset('images/default-avatar.png');
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner && file_exists(storage_path('app/public/' . $this->banner))
            ? asset('storage/' . $this->banner)
            : asset('images/default-banner.png');
    }

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

    public function mostrar($id)
    {
        $user = User::findOrFail($id);
        return view('chat.show', compact('user'));
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_user', 'user_id', 'chat_id')->withTimestamps();
    }
}
