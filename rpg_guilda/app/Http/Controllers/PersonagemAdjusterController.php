<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PersonagemAdjusterController extends Controller
{
    // Campos que podem ser ajustados diretamente
    protected $ajustaveisDiretos = ['vida', 'sanidade', 'sorte'];

    // Atributos armazenados no campo JSON 'atributos'
    protected $atributosJSON = ['forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma'];

    // Ajusta um campo do personagem
    public function adjust(Request $request, Personagem $personagem)
    {
        // Verifica se o usuário é dono do personagem
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Valida o campo e valor
        $camposPermitidos = array_merge($this->ajustaveisDiretos, $this->atributosJSON);
        $validated = $request->validate([
            'campo' => ['required', 'string', Rule::in($camposPermitidos)],
            'valor' => ['required', 'integer'],
        ]);

        $campo = $validated['campo'];
        $valor = $validated['valor'];
        $isJSONField = in_array($campo, $this->atributosJSON);

        try {
            if ($isJSONField) {
                $this->adjustAttribute($personagem, $campo, $valor);
            } else {
                $this->adjustDirectField($personagem, $campo, $valor);
            }

            $personagem->save();

            return redirect()->route('personagens.show', $personagem)
                ->with('success', ucfirst($campo) . ' ajustado em ' . ($valor > 0 ? '+' : '') . $valor . '!');

        } catch (\Exception $e) {
            \Log::error("Erro ao ajustar {$campo} do personagem {$personagem->id}: " . $e->getMessage());

            return back()->with('error', 'Falha ao realizar o ajuste. Verifique o valor e tente novamente.');
        }
    }

    // Ajusta um atributo dentro do JSON 'atributos'
    protected function adjustAttribute(Personagem $personagem, string $atributo, int $valor)
    {
        $atributos = is_string($personagem->atributos) ? json_decode($personagem->atributos, true) : ($personagem->atributos ?? []);

        if (!isset($atributos[$atributo])) {
            throw new \Exception("Atributo '{$atributo}' não encontrado no personagem.");
        }

        $novoValor = max(1, $atributos[$atributo] + $valor); // Limite mínimo 1
        $atributos[$atributo] = $novoValor;

        $personagem->atributos = $atributos;
    }
    
    // Ajusta um campo direto do modelo (vida, sanidade, sorte)
    protected function adjustDirectField(Personagem $personagem, string $campo, int $valor)
    {
        if (is_null($personagem->$campo) && $campo !== 'vida') {
            $personagem->$campo = 0;
        }

        $novoValor = $personagem->$campo + $valor;

        if ($campo === 'vida') {
            $novoValor = max(0, $novoValor); // Limite mínimo 0 para vida
        }

        $personagem->$campo = $novoValor;
    }
}
