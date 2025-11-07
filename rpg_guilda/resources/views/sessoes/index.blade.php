@extends('layouts.app')

@section('title', "Sessões - {$campanha->nome}")

@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-warning m-0">Sessões de {{ $campanha->nome }}</h2>

        {{-- Botão Criar Nova Sessão --}}
        @can('update', $campanha)
            <a href="{{ route('sessoes.create', $campanha->id) }}" class="btn btn-warning fw-bold">
                + Nova Sessão
            </a>
        @endcan
    </div>

    {{-- Mensagens de sucesso --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Lista de Sessões --}}
    @if($sessoes->isEmpty())
        <div class="alert alert-secondary text-center">
            Nenhuma sessão cadastrada ainda.
        </div>
    @else
        <div class="row">
            @foreach($sessoes as $sessao)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-dark text-light border-warning shadow-sm h-100">
                        <div class="card-header bg-warning text-dark fw-bold d-flex justify-content-between">
                            <span>{{ $sessao->titulo }}</span>
                            <span class="badge bg-dark text-warning text-uppercase">
                                {{ ucfirst($sessao->status ?? 'Agendada') }}
                            </span>
                        </div>

                        <div class="card-body">
                            <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}</p>
                            <p><strong>Resumo:</strong> {{ $sessao->resumo ? Str::limit($sessao->resumo, 100) : 'Sem resumo' }}</p>
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('sessoes.show', $sessao->id) }}" class="btn btn-sm btn-outline-warning">
                                Ver
                            </a>

                            @can('update', $campanha)
                                <div class="d-flex gap-2">
                                    <a href="{{ route('sessoes.edit', $sessao->id) }}" class="btn btn-sm btn-outline-light">
                                        Editar
                                    </a>
                                        <form action="{{ route('sessoes.destroy', $sessao->id) }}" method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja excluir esta sessão?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-light">
            ← Voltar para Campanha
        </a>
    </div>
</div>
@endsection
