<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;
use App\Models\Sistema;

class ClassesTableSeeder extends Seeder
{
    public function run(): void
    {
        $sistemas = Sistema::all();

        foreach ($sistemas as $sistema) {
            $classes = [];

            // Classes padrão para D&D 5e
            if ($sistema->nome === 'D&D 5e') {
                $classes = [
                    ['nome' => 'Guerreiro', 'descricao' => 'Classe focada em combate corpo a corpo.'],
                    ['nome' => 'Mago', 'descricao' => 'Classe focada em magia.'],
                    ['nome' => 'Ladino', 'descricao' => 'Especialista em furtividade e habilidades manuais.'],
                    ['nome' => 'Clérigo', 'descricao' => 'Curandeiro e suporte divino.'],
                ];
            }

            // Classes padrão para Call of Cthulhu
            if ($sistema->nome === 'Call of Cthulhu') {
                $classes = [
                    ['nome' => 'Investigador', 'descricao' => 'Personagem comum investigando mistérios.'],
                    ['nome' => 'Acadêmico', 'descricao' => 'Pesquisador ou cientista.'],
                    ['nome' => 'Detetive', 'descricao' => 'Profissional de investigação.'],
                    ['nome' => 'Médico', 'descricao' => 'Especialista em cuidados médicos.'],
                ];
            }

            foreach ($classes as $classe) {
                Classe::firstOrCreate(
                    ['nome' => $classe['nome'], 'sistema_id' => $sistema->id],
                    ['descricao' => $classe['descricao']]
                );
            }
        }
    }
}
