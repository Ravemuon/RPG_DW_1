<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArquivoController extends Controller
{
    /**
     * Retorna a lista de arquivos de uma entidade específica (usuário ou campanha).
     */
    public function index(string $entidade, int $id)
    {
        $query = Arquivo::query();

        match ($entidade) {
            'usuario'  => $query->where('usuario_id', $id),
            'campanha' => $query->where('campanha_id', $id),
            default    => abort(400, 'Entidade inválida.')
        };

        return response()->json(
            $query->orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * Faz o upload de um novo arquivo e o associa a uma entidade.
     */
    public function upload(Request $request, string $entidade, int $id)
    {
        $request->validate([
            'arquivo' => 'required|file|max:10240',
            'tipo'    => 'nullable|string',
        ]);

        $tipo = $request->tipo ?? 'desconhecido';
        $usuario_id = null;
        $campanha_id = null;

        // Verifica a entidade e a permissão
        match ($entidade) {
            'usuario' => $id == Auth::id()
                ? $usuario_id = $id
                : abort(403, 'Não autorizado.'),

            'campanha' => [
                $campanha_id = $id,
                $usuario_id = Auth::id()
            ],

            default => abort(400, 'Entidade inválida.')
        };

        $arquivo = $request->file('arquivo');
        // Salva o arquivo no disco 'public' dentro da pasta da entidade
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
            'arquivo' => $novoArquivo,
        ]);
    }

    /**
     * Faz o download de um arquivo.
     */
    public function download(Arquivo $arquivo)
    {
        // Verifica se o usuário é o proprietário ou um administrador
        if ($arquivo->usuario_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Não autorizado.');
        }

        return Storage::disk('public')->download(
            $arquivo->caminho,
            $arquivo->nome_original
        );
    }

    /**
     * Exclui um arquivo e o remove do storage.
     */
    public function destroy(Arquivo $arquivo)
    {
        // Verifica se o usuário é o proprietário ou um administrador
        if ($arquivo->usuario_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Não autorizado.');
        }

        // Remove o arquivo do storage e o registro do banco de dados
        Storage::disk('public')->delete($arquivo->caminho);
        $arquivo->delete();

        return response()->json(['success' => true]);
    }
}
