<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class MissoesPrioridadeChart extends Chart
{
    public function __construct(array $dashboard)
    {
        parent::__construct();

        $this->labels(['Baixa Prioridade', 'Média Prioridade', 'Alta Prioridade']);

        $this->dataset('Prioridade das Missões', 'doughnut', [
            $dashboard['baixa'],
            $dashboard['media'],
            $dashboard['alta'],
        ])
        ->backgroundColor([
            'rgb(25, 135, 84)',
            'rgb(255, 193, 7)',
            'rgb(220, 53, 69)',
        ]);
    }
}
