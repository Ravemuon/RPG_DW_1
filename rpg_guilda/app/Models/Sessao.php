<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessao extends Model
{
    protected $table = 'sessoes';

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
                    ->withPivot('resultado')
                    ->withTimestamps();
    }

    public function presencas()
    {
        return $this->belongsToMany(User::class, 'sessao_jogador_presenca', 'sessao_id', 'jogador_id')
                    ->withPivot('confirmou_presenca')
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sistema extends Model
{
    use HasFactory;

    protected $table = 'sistemas';

    protected $fillable = [
        'nome',
        'descricao',
        'foco',
        'mecanica_principal',
        'complexidade',
        'atributos',
        'usa_sanidade',
        'formula_pontos_vida',
        'recursos',
        'regras_opcionais',
    ];

    protected $casts = [
        'atributos' => 'array',
        'recursos' => 'array',
        'regras_opcionais' => 'array',
        'usa_sanidade' => 'boolean',
    ];

    public function pericias()
    {
        return $this->hasMany(Pericia::class, 'sistema_id');
    }

    public function classes()
    {
        return $this->hasMany(Classe::class, 'sistema_id');
    }

    public function racas()
    {
        return $this->hasMany(Raca::class, 'sistema_id');
    }

    public function origens()
    {
        return $this->hasMany(Origem::class, 'sistema_id');
    }

    public function personagens()
    {
        return $this->hasMany(Personagem::class, 'sistema_id');
    }
}
