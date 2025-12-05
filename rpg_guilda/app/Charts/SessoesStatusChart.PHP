<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\DB;

/**
 * Classe de Configuração para o Gráfico de Status das Sessões.
 * * Este gráfico mostra a distribuição de sessões por status (Agendada, Concluída, etc.).
 */
class SessoesStatusChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    /**
     * Gera o gráfico de Doughnut com os dados de status das sessões.
     *
     * @param array $statusCounts Array de contagens de status (ex: ['agendada' => 10, 'concluida' => 5])
     * @return \ArielMejiaDev\LarapexCharts\LarapexChart
     */
    public function build(array $statusCounts): \ArielMejiaDev\LarapexCharts\LarapexChart
    {
        // Mapeamento de status amigável para cor e label
        $statusMap = [
            'agendada'      => ['label' => 'Agendada', 'color' => '#4cc9f0'], // Azul Claro
            'em_andamento'  => ['label' => 'Em andamento', 'color' => '#ffc107'], // Amarelo (Warning)
            'concluida'     => ['label' => 'Concluída', 'color' => '#198754'], // Verde (Success)
            'cancelada'     => ['label' => 'Cancelada', 'color' => '#dc3545'], // Vermelho (Danger)
        ];

        $labels = [];
        $data = [];
        $colors = [];

        // Processa as contagens e preenche os arrays
        foreach ($statusMap as $key => $map) {
            $count = $statusCounts[$key] ?? 0;
            if ($count > 0) {
                $labels[] = $map['label'];
                $data[] = $count;
                $colors[] = $map['color'];
            }
        }
        
        // Se não houver dados, retorna um gráfico vazio ou padrão
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
            ->setStroke(1) // Borda
            ->setToolbar(true);
    }
}