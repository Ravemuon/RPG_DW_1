@extends('layouts.app')

@section('title', 'Lista de Perícias')

@section('content')

<div class="container mx-auto p-4">
    <div class="max-w-4xl mx-auto">
        <header class="flex justify-between items-center mb-8 border-b pb-4 border-gray-200 dark:border-gray-700">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">Perícias do Sistema</h1>
            @can('manage-pericias')
                <a href="{{ route('sistemas.pericias.create', $sistema->id) }}"
                   class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-150">
                    <span class="text-xl leading-none mr-1">+</span> Nova Perícia
                </a>
            @endcan
        </header>

        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @if(count($sistema->pericias) > 0)
                    @foreach ($sistema->pericias as $pericia)
                        <li class="p-5 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                            <div class="flex justify-between items-center">
                                <div>
                                    <a href="{{ route('sistemas.pericias.show', [$sistema->id, $pericia->id]) }}"
                                       class="text-xl font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition block">
                                        {{ $pericia->nome }}
                                    </a>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ Str::limit($pericia->descricao, 120) }}
                                    </p>
                                </div>

                                <div class="flex space-x-3">
                                    @can('manage-pericias')
                                        <a href="{{ route('sistemas.pericias.edit', [$sistema->id, $pericia->id]) }}"
                                           class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 text-sm font-semibold">
                                            Editar
                                        </a>
                                    @endcan
                                    <a href="{{ route('sistemas.pericias.show', [$sistema->id, $pericia->id]) }}"
                                       class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-semibold">
                                        Ver
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                @else
                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-700">
                        <p class="text-xl text-gray-500 dark:text-gray-300 mb-4">Nenhuma perícia registrada no sistema.</p>
                        @can('manage-pericias')
                            <a href="{{ route('sistemas.pericias.create', $sistema->id) }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Adicionar Perícia
                            </a>
                        @endcan
                    </div>
                @endif
            </ul>
        </div>
    </div>
</div>

@endsection
