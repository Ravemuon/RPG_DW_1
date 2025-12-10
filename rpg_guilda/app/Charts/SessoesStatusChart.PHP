<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class SessoesStatusChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(array $statusCounts): \ArielMejiaDev\LarapexCharts\LarapexChart
    {
        $statusMap = [
            'agendada'      => ['label' => 'Agendada', 'color' => '#4cc9f0'],
            'em_andamento'  => ['label' => 'Em andamento', 'color' => '#ffc107'],
            'concluida'     => ['label' => 'Concluída', 'color' => '#198754'],
            'cancelada'     => ['label' => 'Cancelada', 'color' => '#dc3545'],
        ];

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($statusMap as $key => $map) {
            $count = $statusCounts[$key] ?? 0;
            if ($count > 0) {
                $labels[] = $map['label'];
                $data[] = $count;
                $colors[] = $map['color'];
            }
        }

        if (empty($data)) {
            return $this->chart->donutChart()
                ->setTitle('Nenhuma Sessão Registrada')
                ->setLabels(['Vazio'])
                ->setDataset([1])
                ->setColors(['#e9ecef'])
                ->setHeight(300);
        }

        return $this->chart->donutChart()
            ->setTitle('Distribuição de Status das Sessões')
            ->addData($data)
            ->setLabels($labels)
            ->setColors($colors)
            ->setHeight(300)
            ->setStroke(1)
            ->setToolbar(true);
    }
}
