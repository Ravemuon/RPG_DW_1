<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArquivoController extends Controller
{
    /**
     * Lista arquivos de uma entidade específica
     *
     * @param string $entidade Tipo de entidade: usuario, campanha, chat, sessao
     * @param int $id ID da entidade
     */
    public function index(string $entidade, int $id)
    {
        $query = Arquivo::query();

        switch ($entidade) {
            case 'usuario':
                $query->where('usuario_id', $id);
                break;
            case 'campanha':
                $query->where('campanha_id', $id);
                break;
            // Pode adicionar chat_id, sessao_id futuramente se criar colunas
            default:
                abort(400, 'Entidade inválida.');
        }

        $arquivos = $query->orderBy('created_at', 'desc')->get();
        return response()->json($arquivos);
    }

    /**
     * Upload de arquivo para uma entidade
     */
    public function upload(Request $request, string $entidade, int $id)
    {
        $request->validate([
            'arquivo' => 'required|file|max:10240', // 10MB máximo
            'tipo'    => 'nullable|string'
        ]);

        $tipo = $request->tipo ?? 'desconhecido';

        // Determinar o dono/entidade
        $usuario_id = null;
        $campanha_id = null;

        switch ($entidade) {
            case 'usuario':
                if ($id != Auth::id()) {
                    abort(403, 'Não autorizado.');
                }
                $usuario_id = $id;
                break;
            case 'campanha':
                // Aqui pode-se validar se o usuário pertence à campanha
                $campanha_id = $id;
                $usuario_id = Auth::id();
                break;
            default:
                abort(400, 'Entidade inválida.');
        }

        $arquivo = $request->file('arquivo');
        $path = $arquivo->store($entidade, 'public');

        $novoArquivo = Arquivo::create([
            'usuario_id'    => $usuario_id,
            'campanha_id'   => $campanha_id,
            'nome_original' => $arquivo->getClientOriginalName(),
            'caminho'       => $path,
            'tipo'          => $tipo,
            'tamanho'       => $arquivo->getSize(),
        ]);

        return response()->json([
            'success' => true,
            'arquivo' => $novoArquivo
        ]);
    }

    /**
     * Download de arquivo
     */
    public function download(Arquivo $arquivo)
    {
        // Pode validar permissão aqui
        if ($arquivo->usuario_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Não autorizado.');
        }

        return Storage::disk('public')->download($arquivo->caminho, $arquivo->nome_original);
    }

    /**
     * Deleta um arquivo
     */
    public function destroy(Arquivo $arquivo)
    {
        if ($arquivo->usuario_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Não autorizado.');
        }

        Storage::disk('public')->delete($arquivo->caminho);
        $arquivo->delete();

        return response()->json(['success' => true]);
    }
}
