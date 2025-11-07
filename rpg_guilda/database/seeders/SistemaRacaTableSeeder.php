<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Sistema;
use App\Models\Raca;

class SistemaRacaTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Populando tabela sistema_raca...');

        // Busca todos os sistemas e raças já cadastrados
        $sistemas = Sistema::all();
        $racas = Raca::all();

        foreach ($sistemas as $sistema) {
            foreach ($racas as $raca) {
                // Insere apenas se não existir para evitar duplicidade
                DB::table('sistema_raca')->updateOrInsert([
                    'sistema_id' => $sistema->id,
                    'raca_id' => $raca->id,
                ]);
            }
        }

        $this->command->info('Tabela sistema_raca populada com sucesso!');
    }
}
