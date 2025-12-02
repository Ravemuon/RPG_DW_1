<?php

namespace App\Charts;

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
    }
}
