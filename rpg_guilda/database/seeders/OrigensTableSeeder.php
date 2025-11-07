<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Origem;
use App\Models\Sistema;

class OrigensTableSeeder extends Seeder
{
    public function run(): void
    {
        $sistemas = Sistema::all();

        foreach ($sistemas as $sistema) {
            $origens = [];

            // Exemplo D&D 5e
            if ($sistema->nome === 'D&D 5e') {
                $origens = [
                    ['nome' => 'Aldeão', 'descricao' => 'Origem simples de aldeão.'],
                    ['nome' => 'Nobre', 'descricao' => 'Origem nobre e influente.'],
                ];
            }

            // Exemplo Call of Cthulhu
            if ($sistema->nome === 'Call of Cthulhu') {
                $origens = [
                    ['nome' => 'Acadêmico', 'descricao' => 'Pesquisador ou cientista.'],
                    ['nome' => 'Detetive', 'descricao' => 'Profissional de investigação.'],
                    ['nome' => 'Médico', 'descricao' => 'Especialista em cuidados médicos.'],
                ];
            }

            foreach ($origens as $origem) {
                Origem::firstOrCreate(
                    ['nome' => $origem['nome'], 'sistema_id' => $sistema->id],
                    ['descricao' => $origem['descricao']]
                );
            }
        }
    }
}
