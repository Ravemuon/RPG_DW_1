<?php

namespace App\Charts;

<<<<<<< HEAD
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

    /**
     * Gera um gráfico de linha (para Index) ou Donut (para Show).
     *
     * @param Campanha $campanha
     * @param Collection<Sessao>|Sessao $sessoes Coleção de Sessões (index) ou uma única Sessão (show).
     */
    public function handler(Campanha $campanha, $sessoes)
    {
        // Se for uma única sessão (para o método 'show'), chama o gráfico Donut.
        if ($sessoes instanceof Sessao) {
            return $this->buildSingleSessionChart($campanha, $sessoes);
        }

        // Se for uma coleção, constrói o gráfico de Linha (index).
        return $this->buildEngagementChart($campanha, $sessoes);
    }

    /**
     * Constrói o gráfico de linha de engajamento ao longo do tempo (Index).
     */
    protected function buildEngagementChart(Campanha $campanha, Collection $sessoes)
    {
        $sessoesConcluidas = $sessoes->filter(fn($s) => $s->status === 'concluida')->sortBy('data_hora');

        if ($sessoesConcluidas->isEmpty()) {
            return $this->buildEmptyChart();
        }

        $pivotTableName = 'sessao_jogador_presenca';

        // 1. Coleta dados de presença por sessão
        $presencas = DB::table($pivotTableName)
            ->whereIn('sessao_id', $sessoesConcluidas->pluck('id'))
            ->groupBy('sessao_id')
            ->select('sessao_id', DB::raw('count(jogador_id) as total_presencas'))
            ->pluck('total_presencas', 'sessao_id');

        // 2. Total de jogadores ativos na campanha (exclui o mestre)
        $totalJogadoresAtivos = $campanha->jogadores()
            ->where('user_id', '!=', $campanha->criador_id)
            ->wherePivot('status', 'ativo')
            ->count();
        $maximoDeJogadores = max(1, $totalJogadoresAtivos);

        // 3. Prepara os dados
        $labels = [];
        $presencasCount = [];
        $taxaPresencas = [];

        foreach ($sessoesConcluidas as $sessao) {
            $labels[] = "Sessão #{$sessao->id}";
            $count = $presencas->get($sessao->id, 0);
            $presencasCount[] = $count;

            $taxaPresenca = round(($count / $maximoDeJogadores) * 100, 1);
            $taxaPresencas[] = $taxaPresenca;
        }

        // 4. Gera o Gráfico de Linha
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
     * Constrói um gráfico Donut para uma única sessão (Show).
     */
    protected function buildSingleSessionChart(Campanha $campanha, Sessao $sessao)
    {
        $presencasConfirmadas = $sessao->presencas()->count();
        $totalJogadoresAtivos = $campanha->jogadores()
            ->where('user_id', '!=', $campanha->criador_id)
            ->wherePivot('status', 'ativo')
            ->count();

        $ausencias = max(0, $totalJogadoresAtivos - $presencasConfirmadas);
        $total = $presencasConfirmadas + $ausencias;

        return $this->chart->donutChart()
            ->setTitle("Presença na Sessão #{$sessao->id}")
            ->setSubtitle("Total de Jogadores: {$total}")
            ->addData([$presencasConfirmadas, $ausencias])
            ->setLabels(['Presentes', 'Ausentes'])
            ->setColors(['#4CAF50', '#F44336'])
            ->setHeight(300);
    }

    /**
     * Constrói um gráfico de Linha vazio.
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
=======
use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use App\Models\Sessao;

class SessaoPresencasChart extends BaseChart
{
    public function handler(): Chartisan
    {
        $sessoes = Sessao::orderBy('data_hora', 'asc')->get();

        return Chartisan::build()
            ->labels($sessoes->pluck('titulo')->toArray())
            ->dataset('Confirmados', $sessoes->map(fn($s) => $s->presencas->count())->toArray());
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
    }
}
