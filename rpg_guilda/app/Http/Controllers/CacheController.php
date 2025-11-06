<?php

namespace App\Http\Controllers;

use App\Models\Cache;
use Illuminate\Http\Request;

class CacheController extends Controller
{
    // Listar todos os itens do cache
    public function index()
    {
        $caches = Cache::all();
        return view('cache.index', compact('caches'));
    }

    // Criar ou atualizar um item do cache
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

    // Deletar item do cache
    public function destroy($key)
    {
        Cache::where('key', $key)->delete();
        return redirect()->back()->with('success', 'Cache removido com sucesso.');
    }
}
