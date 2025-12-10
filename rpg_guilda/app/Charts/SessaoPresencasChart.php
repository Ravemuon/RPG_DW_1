<?php

namespace App\Charts;

use App\Models\Campanha;
use App\Models\Sessao;
use Illuminate\Support\Collection;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\DB;

class SessaoPresencasChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function handler(Campanha $campanha, $sessoes)
    {
        if ($sessoes instanceof Sessao) {
            return $this->buildSingleSessionChart($campanha, $sessoes);
        }

        return $this->buildEngagementChart($campanha, $sessoes);
    }

    protected function buildEngagementChart(Campanha $campanha, Collection $sessoes)
    {
        $sessoesConcluidas = $sessoes->filter(fn($s) => $s->status === 'concluida')->sortBy('data_hora');

        if ($sessoesConcluidas->isEmpty()) {
            return $this->buildEmptyChart();
        }

        $pivotTableName = 'sessao_jogador_presenca';
        $presencas = DB::table($pivotTableName)
            ->whereIn('sessao_id', $sessoesConcluidas->pluck('id'))
            ->groupBy('sessao_id')
            ->select('sessao_id', DB::raw('count(jogador_id) as total_presencas'))
            ->pluck('total_presencas', 'sessao_id');

        $totalJogadoresAtivos = $campanha->jogadores()
            ->wherePivot('status', 'ativo')
            ->where('user_id', '!=', $campanha->criador_id)
            ->count();

        $maximoDeJogadores = max(1, $totalJogadoresAtivos);

        $labels = [];
        $presencasCount = [];
        $taxaPresencas = [];

        foreach ($sessoesConcluidas as $sessao) {
            $labels[] = "Sessão #{$sessao->id}";
            $count = $presencas->get($sessao->id, 0);
            $presencasCount[] = $count;
            $taxaPresencas[] = round(($count / $maximoDeJogadores) * 100, 1);
        }

        return $this->chart->lineChart()
            ->setTitle('Engajamento dos Jogadores (Taxa de Presença)')
            ->setSubtitle("Base: {$maximoDeJogadores} Jogadores Ativos")
            ->addData('Presenças Confirmadas', $presencasCount)
            ->addData('Taxa de Presença (%)', $taxaPresencas)
            ->setXAxis($labels)
            ->setGrid()
            ->setMarkers(['#FF5722', '#E040FB'], 7, 10)
            ->setHeight(350);
    }

    protected function buildSingleSessionChart(Campanha $campanha, Sessao $sessao)
    {
        $totalJogadoresAtivos = $campanha->jogadores()
            ->wherePivot('status', 'ativo')
            ->where('user_id', '!=', $campanha->criador_id)
            ->count();

        $presencasConfirmadas = $sessao->presencas()->count();
        $ausencias = max(0, $totalJogadoresAtivos - $presencasConfirmadas);
        $total = $presencasConfirmadas + $ausencias;

        if ($total === 0) {
            return $this->buildEmptyChart();
        }

        return $this->chart->donutChart()
            ->setTitle("Presença na Sessão #{$sessao->id}")
            ->setSubtitle("Total de Jogadores: {$total}")
            ->addData([$presencasConfirmadas, $ausencias])
            ->setLabels(['Presentes', 'Ausentes'])
            ->setColors(['#4CAF50', '#F44336'])
            ->setHeight(300);
    }

    protected function buildEmptyChart(): \ArielMejiaDev\LarapexCharts\LineChart
    {
        return $this->chart->lineChart()
            ->setTitle('Engajamento dos Jogadores')
            ->setSubtitle('Nenhuma sessão concluída ainda.')
            ->addData('Presenças Confirmadas', [0])
            ->addData('Taxa de Presença (%)', [0])
            ->setXAxis([''])
            ->setGrid()
            ->setHeight(350);
    }
}
