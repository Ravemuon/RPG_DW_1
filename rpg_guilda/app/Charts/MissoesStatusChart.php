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
        ]);
    }
}
