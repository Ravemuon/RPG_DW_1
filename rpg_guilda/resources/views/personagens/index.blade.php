@extends('layouts.app')

@section('title', 'Meus Personagens')

@section('content')

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
<div class="flex justify-between items-center mb-6">
<h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Meus Personagens</h1>
<a href="{{ route('personagens.create', ['campanha' => request()->query('campanha')]) }}"
class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-xl hover:bg-indigo-700 transition duration-300 ease-in-out transform hover:scale-105 border-b-4 border-indigo-800">
Criar Novo Personagem
</a>
</div>

@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-md" role="alert">
        {{ session('success') }}
    </div>
@endif

@if ($personagens->isEmpty())
    <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-2xl text-center border border-gray-200 dark:border-gray-700">
        <p class="text-gray-500 dark:text-gray-400 text-lg">Você ainda não tem personagens. Crie o seu primeiro herói!</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($personagens as $personagem)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden transform transition duration-500 hover:scale-[1.02] hover:shadow-indigo-500/50 border border-gray-100 dark:border-gray-700">
                <div class="relative h-40">
                    @if ($personagem->imagem)
                        <img src="{{ Storage::url($personagem->imagem) }}" alt="Imagem de {{ $personagem->nome }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-700">
                            <span class="text-gray-500 text-xl font-bold">Sem Imagem</span>
                        </div>
                    @endif
                    <span class="absolute top-3 left-3 bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">{{ $personagem->raca->nome ?? 'Raça Desconhecida' }}</span>
                    <span class="absolute bottom-3 right-3 bg-red-600 text-white text-sm font-extrabold px-3 py-1 rounded-lg shadow-lg">PV: {{ $personagem->pv_atual ?? 0 }}/{{ $personagem->pv_maximo ?? 0 }}</span>
                </div>

                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 truncate">{{ $personagem->nome }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $personagem->classe->nome ?? 'Classe Desconhecida' }} - Nível {{ $personagem->nivel ?? 1 }}</p>

                    <div class="flex justify-between text-sm mb-4">
                        <span class="text-gray-700 dark:text-gray-300">Campanha: <span class="font-semibold text-indigo-600">{{ $personagem->campanha->nome ?? 'Solo' }}</span></span>
                        <span class="text-gray-700 dark:text-gray-300">Sistema: <span class="font-semibold text-indigo-600">{{ $personagem->sistema->nome ?? 'N/A' }}</span></span>
                    </div>

                    <div class="flex space-x-3 justify-end">
                        <a href="{{ route('personagens.show', $personagem) }}" class="text-indigo-600 hover:text-indigo-800 font-medium transition duration-300 ease-in-out">Ver Ficha</a>
                        <a href="{{ route('personagens.edit', $personagem) }}" class="text-yellow-600 hover:text-yellow-800 font-medium transition duration-300 ease-in-out">Editar</a>
                        <form action="{{ route('personagens.destroy', $personagem) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar {{ $personagem->nome }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium transition duration-300 ease-in-out">Deletar</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif


</div>

@endsection
