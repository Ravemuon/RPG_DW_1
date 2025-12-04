<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class MissoesStatusChart extends Chart
{
    public function __construct(array $dashboard)
    {
        parent::__construct();

        $this->labels(['Pendente', 'Em Andamento', 'Concluída', 'Cancelada']);

        $this->dataset('Status das Missões', 'bar', [
            $dashboard['pendente'],
            $dashboard['em_andamento'],
            $dashboard['concluida'],
            $dashboard['cancelada'],
        ])
        ->backgroundColor([
            'rgb(108, 117, 125)',  // secondary
            'rgb(13, 110, 253)',   // info
            'rgb(25, 135, 84)',    // success
            'rgb(220, 53, 69)',    // danger
        ]);
    }
}
