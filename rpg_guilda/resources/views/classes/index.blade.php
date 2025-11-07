@extends('layouts.app')

@section('title', 'Classes de Personagem')

@section('content')

<div class="container mx-auto p-4">
<div class="max-w-6xl mx-auto">
<header class="flex justify-between items-center mb-8 border-b pb-4 border-gray-200 dark:border-gray-700">
<h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">Classes de Personagem</h1>
@can('manage-classes')
<a href="{{ route('classes.create') }}"
class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-150">
<span class="text-xl leading-none mr-1">+</span> Nova Classe
</a>
@endcan
</header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        {{-- Substitua $classes por dados reais do controlador --}}
        @if(count($classes) > 0)
            @foreach ($classes as $classe)
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-5 hover:shadow-xl transition duration-300 border-t-4 border-indigo-500">
                    <a href="{{ route('classes.show', $classe) }}"
                       class="text-xl font-bold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition block mb-2">
                        {{ $classe->nome }}
                    </a>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ Str::limit($classe->descricao, 80) }}
                    </p>
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">
                            Foco: {{ $classe->atributos_primarios ?? 'N/A' }}
                        </span>
                        <a href="{{ route('classes.show', $classe) }}"
                           class="text-sm font-semibold text-indigo-500 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="lg:col-span-4 text-center py-12 bg-gray-50 dark:bg-gray-700 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                <p class="text-xl text-gray-500 dark:text-gray-300 mb-4">Nenhuma classe de personagem registrada.</p>
                @can('manage-classes')
                    <a href="{{ route('classes.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Adicionar Classe
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>


</div>
@endsection
