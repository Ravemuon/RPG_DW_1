<?php

namespace App\Http\Controllers;

use App\Models\Cache;
use Illuminate\Http\Request;

class CacheController extends Controller
{
    // Exibe a lista de todos os itens de cache
    public function index()
    {
        $caches = Cache::all();
        return view('cache.index', compact('caches'));
    }

    // Cria ou atualiza um item de cache baseado na chave fornecida
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'expiration' => 'required|integer|min:0',
        ]);

        Cache::updateOrCreate(
            ['key' => $request->key],
            [
                'value' => $request->value,
                'expiration' => $request->expiration
            ]
        );

        return redirect()->back()->with('success', 'Cache atualizado com sucesso.');
    }

    // Remove um item de cache pela chave
    public function destroy($key)
    {
        Cache::where('key', $key)->delete();
        return redirect()->back()->with('success', 'Cache removido com sucesso.');
    }
}
