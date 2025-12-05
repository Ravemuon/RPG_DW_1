@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            @include('personagens.create._progress_bar', ['data' => $sessionData])
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4 border-dark">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">6. Revisão Final e Criação</h4>
                </div>
                <div class="card-body">
                    <p class="lead">Revise os dados abaixo. Se tudo estiver correto, clique em **Finalizar Criação** para salvar seu personagem permanentemente no banco de dados.</p>
                    
                    <h5 class="text-primary mt-4">Sumário dos Dados:</h5>
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            **Nome:**
                            <span class="badge bg-success">{{ $sessionData['nome'] ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            **Raça/Classe/Origem:**
                            <span class="badge bg-warning text-dark">{{ $raca->nome ?? 'N/A' }} / {{ $classe->nome ?? 'N/A' }} / {{ $origem->nome ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            **Pontos de Vida (PV):**
                            <span class="badge bg-danger">{{ $sessionData['vida'] ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            **Atributos:**
                            <span class="badge bg-primary">{{ count($atributos) }} definidos</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            **Perícias:**
                            <span class="badge bg-secondary">{{ count($pericias) }} selecionadas</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            **Campanha/Sistema:**
                            <span class="badge bg-info">{{ $campanha->nome ?? 'N/A' }} ({{ $sistema->nome ?? 'N/A' }})</span>
                        </li>
                    </ul>

                    <div class="alert alert-info">
                        **Observação:** Depois de finalizar, você poderá editar a ficha do personagem através da visão de *overview* de edição.
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('personagens.step5') }}" class="btn btn-outline-secondary">
                            &laquo; Voltar (Passo 5)
                        </a>
                        <form method="POST" action="{{ route('personagens.storeFinal') }}">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-lg">
                                🚀 Finalizar Criação
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection