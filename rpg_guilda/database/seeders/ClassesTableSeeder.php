<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sistema;
use App\Models\Classe;

class ClassesTableSeeder extends Seeder
{
    public function run(): void
    {
        $sistema = Sistema::first(); // pega o primeiro sistema cadastrado

        if (!$sistema) {
            $this->command->warn('Nenhum sistema encontrado. Classes não criadas.');
            return;
        }

        $classes = [
            [
                'nome' => 'Guerreiro',
                'descricao' => 'Classe focada em combate corpo a corpo',
                'sistema_id' => $sistema->id,
            ],
            [
                'nome' => 'Mago',
                'descricao' => 'Classe focada em magia',
                'sistema_id' => $sistema->id,
            ],
        ];

        foreach ($classes as $classe) {
            Classe::create($classe);
        }
    }
}
