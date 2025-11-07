@extends('layouts.app')

@section('title', 'Missão: ' . $missao->titulo)

@section('content')

<div class="container mx-auto p-4">
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-lg p-8">

    <header class="mb-6 border-b pb-4 border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100">{{ $missao->titulo }}</h1>
                <p class="text-xl text-indigo-600 dark:text-indigo-400 mt-1">Campanha: <a href="{{ route('campanhas.show', $campanha) }}" class="hover:underline">{{ $campanha->nome }}</a></p>
            </div>
            @can('manage-missao', $campanha)
                <div class="flex space-x-2">
                    <a href="{{ route('missoes.edit', [$campanha, $missao]) }}"
                       class="px-3 py-1 bg-yellow-500 text-white text-sm font-semibold rounded-md hover:bg-yellow-600 transition">
                        Editar
                    </a>
                    {{-- Botão de Excluir (apenas para o mestre) --}}
                </div>
            @endcan
        </div>
    </header>

    <section class="space-y-6">
        <div class="p-4 rounded-lg
            @if($missao->status == 'concluida') bg-green-100 dark:bg-green-900 border-green-400
            @elseif($missao->status == 'em_andamento') bg-blue-100 dark:bg-blue-900 border-blue-400
            @else bg-gray-100 dark:bg-gray-700 border-gray-400
            @endif
            border-l-4">
            <p class="text-sm font-medium
                @if($missao->status == 'concluida') text-green-800 dark:text-green-200
                @elseif($missao->status == 'em_andamento') text-blue-800 dark:text-blue-200
                @else text-gray-800 dark:text-gray-200
                @endif">
                <strong>Status Atual:</strong> {{ ucfirst(str_replace('_', ' ', $missao->status)) }}
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Objetivo e Detalhes</h2>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md shadow-inner">
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $missao->descricao ?? 'Nenhum detalhe adicional fornecido.' }}</p>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Recompensas (Placeholder)</h2>
            <ul class="list-disc list-inside ml-4 text-gray-700 dark:text-gray-300">
                <li>XP: {{ $missao->recompensa_xp ?? 'Não definido' }}</li>
                <li>Ouro: {{ $missao->recompensa_ouro ?? 'Não definido' }}</li>
                <li>Item Especial: *...*</li>
            </ul>
        </div>
    </section>

</div>


</div>
@endsection
