<?php

namespace App\Charts;

use App\Models\Campanha;
use App\Models\Sessao;
use Illuminate\Support\Collection;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\DB;

/**
 * Classe responsável por gerar os gráficos de presença e engajamento das sessões.
 *
 * O gráfico de Engajamento (Index) é de Linha, mostrando a taxa de presença ao longo do tempo.
 * O gráfico de Sessão Única (Show) é Donut, mostrando a divisão de presença/ausência.
 */
class SessaoPresencasChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    /**
     * Gera gráfico de linha (index) ou donut (show) dependendo do parâmetro.
     *
     * @param Campanha $campanha A campanha atual.
     * @param Sessao|\Illuminate\Support\Collection $sessoes Uma única Sessão ou uma Coleção de Sessões.
     * @return \ArielMejiaDev\LarapexCharts\LarapexChart
     */
    public function handler(Campanha $campanha, $sessoes)
    {
        // Se for uma única sessão (página de detalhes)
        if ($sessoes instanceof Sessao) {
            return $this->buildSingleSessionChart($campanha, $sessoes);
        }

        // Se for uma coleção de sessões (página de índice)
        return $this->buildEngagementChart($campanha, $sessoes);
    }

    /**
     * Constrói o gráfico de linha de Engajamento dos Jogadores ao longo do tempo.
     *
     * @param Campanha $campanha
     * @param \Illuminate\Support\Collection $sessoes
     * @return \ArielMejiaDev\LarapexCharts\LarapexChart
     */
    protected function buildEngagementChart(Campanha $campanha, Collection $sessoes)
    {
        // Filtra e ordena apenas sessões concluídas
        $sessoesConcluidas = $sessoes->filter(fn($s) => $s->status === 'concluida')->sortBy('data_hora');

        if ($sessoesConcluidas->isEmpty()) {
            return $this->buildEmptyChart();
        }

        $pivotTableName = 'sessao_jogador_presenca';

        // Coleta número de presenças por sessão usando DB para eficiência
        $presencas = DB::table($pivotTableName)
            ->whereIn('sessao_id', $sessoesConcluidas->pluck('id'))
            ->groupBy('sessao_id')
            ->select('sessao_id', DB::raw('count(jogador_id) as total_presencas'))
            ->pluck('total_presencas', 'sessao_id');

        // Total de jogadores ativos na campanha, excluindo o mestre (criador)
        // Assume-se que o relacionamento 'jogadores' tem o pivot 'status'
        $totalJogadoresAtivos = $campanha->jogadores()
            ->wherePivot('status', 'ativo')
            ->where('user_id', '!=', $campanha->criador_id)
            ->count();
            
        // Garante que o divisor não seja zero
        $maximoDeJogadores = max(1, $totalJogadoresAtivos);

        $labels = [];
        $presencasCount = [];
        $taxaPresencas = [];

        foreach ($sessoesConcluidas as $sessao) {
            $labels[] = "Sessão #{$sessao->id}";
            $count = $presencas->get($sessao->id, 0); // Presenças confirmadas
            $presencasCount[] = $count;
            // Calcula a taxa percentual de presença
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

    /**
     * Constrói o gráfico Donut para uma única sessão, mostrando Presença vs. Ausência.
     *
     * @param Campanha $campanha
     * @param Sessao $sessao
     * @return \ArielMejiaDev\LarapexCharts\LarapexChart
     */
    protected function buildSingleSessionChart(Campanha $campanha, Sessao $sessao)
    {
        // Total de jogadores ativos na campanha, excluindo o mestre (criador)
        $totalJogadoresAtivos = $campanha->jogadores()
            ->wherePivot('status', 'ativo')
            ->where('user_id', '!=', $campanha->criador_id)
            ->count();

        $presencasConfirmadas = $sessao->presencas()->count();
        
        // Calcula ausências em relação aos jogadores ativos
        $ausencias = max(0, $totalJogadoresAtivos - $presencasConfirmadas);
        $total = $presencasConfirmadas + $ausencias;

        // Se o total de jogadores ativos for zero, retorna um gráfico vazio ou nulo
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

    /**
     * Constrói um gráfico de linha vazio para o caso de não haver sessões concluídas.
     *
     * @return \ArielMejiaDev\LarapexCharts\LineChart
     */
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