<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rolagem extends Model
{
    protected $table = 'rolagens';

    protected $fillable = [
        'user_id',
        'campanha_id',
        'personagem_id',
        'tipo_dado',
        'quantidade',
        'modificador',
        'resultado',
        'descricao',
        'tipo_rolagem',
    ];

    /* ------------------- Relacionamentos ------------------- */

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function campanha()
    {
        return $this->belongsTo(Campanha::class, 'campanha_id');
    }

    public function personagem()
    {
        return $this->belongsTo(Personagem::class, 'personagem_id');
    }

    /* ------------------- Lógica ------------------- */

    // Executa uma rolagem de dados
    public static function rolar(string $tipoDado, int $quantidade = 1, int $modificador = 0): int
    {
        preg_match('/d(\d+)/i', $tipoDado, $matches);
        $faces = $matches[1] ?? 6;

        $total = 0;
        for ($i = 0; $i < $quantidade; $i++) {
            $total += rand(1, (int)$faces);
        }

        return $total + $modificador;
    }
}
