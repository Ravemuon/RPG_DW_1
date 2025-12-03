<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class MissoesStatusChart extends Chart
{
    public function __construct(array $dashboard)
    {
        parent::__construct();

        $this->labels(['Pendentes', 'Em Andamento', 'Concluídas', 'Canceladas']);

        $this->dataset('Status das Missões', 'doughnut', [
            $dashboard['pendentes'],
            $dashboard['andamento'],
            $dashboard['concluidas'],
            $dashboard['canceladas'],
        ])
        ->backgroundColor([
            'rgb(108, 117, 125)', // secondary (pendentes)
            'rgb(13, 202, 240)',  // info (andamento)
            'rgb(25, 135, 84)',   // success (concluidas)
            'rgb(220, 53, 69)'    // danger (canceladas)
        ]);
    }
}
