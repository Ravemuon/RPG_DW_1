@extends('layouts.app')

@section('title', 'Sessões de ' . $campanha->nome)

@section('content')

<div class="container mx-auto p-4">
<div class="max-w-4xl mx-auto">
<header class="flex justify-between items-center mb-6 border-b pb-4 border-gray-200 dark:border-gray-700">
<h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Relatórios de Sessão da Campanha "{{ $campanha->nome }}"</h1>
@can('manage-sessao', $campanha)
<a href="{{ route('sessao.relatorio', $campanha) }}"
class="px-4 py-2 bg-green-600 text-white font-semibold rounded-md shadow-md hover:bg-green-700 transition">
Novo Relatório
</a>
@endcan
</header>

    <div class="space-y-4">
        @forelse ($sessoes as $sessao)
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-5 flex justify-between items-center hover:shadow-xl transition duration-300">
                <div>
                    <a href="{{ route('sessao.show', $sessao) }}" class="text-xl font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        {{ $sessao->titulo }}
                    </a>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Sessão em: {{ \Carbon\Carbon::parse($sessao->data_sessao)->format('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                        Resumo: {{ Str::limit($sessao->resumo, 80) }}
                    </span>
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-gray-500 dark:text-gray-300">Ainda não há relatórios de sessão para esta campanha.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{-- {{ $sessoes->links() }} --}}
    </div>
</div>


</div>
@endsection
