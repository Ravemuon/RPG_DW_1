@extends('layouts.app')

@section('title', 'Meus Personagens')

@section('content')

<div class="container mx-auto p-4">
<div class="max-w-6xl mx-auto">
<header class="flex justify-between items-center mb-8 border-b pb-4 border-gray-200 dark:border-gray-700">
<h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">Meus Personagens</h1>
<a href="{{ route('personagens.create') }}"
class="px-5 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-150">
<span class="text-xl leading-none mr-1">+</span> Novo Personagem
</a>
</header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Substitua $personagens por dados reais do controlador --}}
        @if(count($personagens) > 0)
            @foreach ($personagens as $personagem)
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden hover:shadow-2xl transition duration-300 transform hover:-translate-y-1">
                    <div class="p-5">
                        <a href="{{ route('personagens.show', $personagem) }}"
                           class="text-xl font-bold text-indigo-600 dark:text-indigo-400 hover:underline transition block mb-2">
                            {{ $personagem->nome }}
                        </a>
                        <p class="text-gray-700 dark:text-gray-300 text-sm">
                            Classe: <span class="font-medium">{{ $personagem->classe->nome ?? 'N/A' }}</span>
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 text-sm mt-1">
                            Nível: <span class="font-medium">{{ $personagem->nivel ?? 1 }}</span>
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 text-sm mt-1">
                            Campanha: <span class="font-medium">{{ $personagem->campanha->nome ?? 'Solo' }}</span>
                        </p>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 p-3 flex justify-end space-x-2">
                        <a href="{{ route('personagens.edit', $personagem) }}"
                           class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 text-sm font-semibold">
                            Editar
                        </a>
                        <a href="{{ route('personagens.show', $personagem) }}"
                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-semibold">
                            Ficha
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="lg:col-span-3 text-center py-12 bg-gray-50 dark:bg-gray-700 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                <p class="text-xl text-gray-500 dark:text-gray-300 mb-4">Você ainda não criou nenhum personagem.</p>
                <a href="{{ route('personagens.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Começar a Criação
                </a>
            </div>
        @endif
    </div>

    <div class="mt-8">
        {{-- {{ $personagens->links() }} --}}
    </div>
</div>


</div>
@endsection
