<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessao extends Model
{
    protected $table = 'sessoes';
    protected $dates = ['data'];

    protected $fillable = [
        'campanha_id',
        'titulo',
        'data_hora',
        'status',
        'criado_por',
        'resumo'
    ];

    protected $casts = [
        'data_hora' => 'datetime',
        'resumo' => 'string',
    ];

    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function personagens()
    {
        return $this->belongsToMany(Personagem::class, 'sessoes_personagens')
                    ->withPivot('presente', 'resultado')
                    ->withTimestamps();
    }

    public function scopeAgendadas($query)
    {
        return $query->where('status', 'agendada');
    }

    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }

    public function scopeConcluidas($query)
    {
        return $query->where('status', 'concluida');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('status', 'cancelada');
    }
}
