@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        {{-- Barra de Progresso --}}
        <div class="col-md-4">
            @include('personagens.create._progress_bar', ['data'=>$sessionData])
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4 border-dark">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">6. Revisão Final</h4>
                </div>
                <div class="card-body">
                    <p class="lead">
                        Revise os dados antes de finalizar.
                    </p>

                    {{-- Imagem --}}
                    @if(!empty($sessionData['imagem_temp_path']))
                        <div class="text-center mb-4">
                            <img src="{{ Storage::url($sessionData['imagem_temp_path']) }}" 
                                 class="img-fluid rounded shadow" 
                                 style="max-height: 250px;">
                        </div>
                    @endif

                    {{-- Sumário --}}
                    <ul class="list-group mb-4">
                        <li class="list-group-item"><strong>Nome:</strong> {{ $sessionData['nome'] ?? 'N/A' }}</li>
                        <li class="list-group-item"><strong>Raça:</strong> {{ $raca->nome ?? 'N/A' }}</li>
                        <li class="list-group-item"><strong>Classe:</strong> {{ $classe->nome ?? 'N/A' }}</li>
                        <li class="list-group-item"><strong>Origem:</strong> {{ $origem->nome ?? 'N/A' }}</li>
                        <li class="list-group-item"><strong>Atributos:</strong> {{ count($atributos) }} definidos</li>
                        <li class="list-group-item"><strong>Perícias:</strong> {{ count($pericias) }} selecionadas</li>
                        <li class="list-group-item"><strong>Campanha:</strong> {{ $campanha->nome ?? 'N/A' }}</li>
                    </ul>

                    {{-- Botões --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('personagens.step5') }}" class="btn btn-outline-secondary">&laquo; Voltar</a>

                        <form method="POST" action="{{ route('personagens.storeFinal') }}">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-lg">🚀 Finalizar Criação</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
