<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    // Relações
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

    /**
     * Rola os dados de RPG e retorna o resultado final.
     *
     * @param string $tipoDado Ex: d6, d20, d100
     * @param int $quantidade Número de dados
     * @param int $modificador Modificador a ser somado
     * @return int Resultado final
     */
    public static function rolar(string $tipoDado, int $quantidade = 1, int $modificador = 0): int
    {
        preg_match('/d(\d+)/i', $tipoDado, $matches);
        $faces = $matches[1] ?? 6; // default d6 se inválido

        $total = 0;
        for ($i = 0; $i < $quantidade; $i++) {
            $total += rand(1, (int)$faces);
        }

        return $total + $modificador;
    }
}
