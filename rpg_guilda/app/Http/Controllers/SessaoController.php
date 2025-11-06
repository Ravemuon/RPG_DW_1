<?php
namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Personagem;
use Illuminate\Http\Request;

class SessaoController extends Controller
{
    public function index($campanha_id)
    {
        $sessoes = Sessao::where('campanha_id', $campanha_id)
            ->with('personagens')
            ->get();
        return response()->json($sessoes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'campanha_id' => 'required|exists:campanhas,id',
            'titulo' => 'required|string|max:255',
            'data_hora' => 'required|date',
        ]);

        $sessao = Sessao::create($request->all());

        // Notificar jogadores da campanha
        // $this->notificarJogadores($sessao);

        return response()->json($sessao);
    }

    public function marcarPresenca($sessao_id, $personagem_id)
    {
        $sessao = Sessao::findOrFail($sessao_id);
        $sessao->personagens()->updateExistingPivot($personagem_id, ['presente' => true]);
        return response()->json(['message' => 'Presença confirmada!']);
    }

    public function concluirSessao(Request $request, $sessao_id)
    {
        $sessao = Sessao::findOrFail($sessao_id);
        $sessao->status = 'concluida';
        $sessao->resumo = $request->resumo;
        $sessao->save();

        // Registrar resultados
        if($request->resultados){
            foreach($request->resultados as $personagem_id => $resultado){
                $sessao->personagens()->updateExistingPivot($personagem_id, ['resultado' => $resultado]);
            }
        }

        return response()->json($sessao->load('personagens'));
    }
}
