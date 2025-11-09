@extends('layouts.app')

@section('title', 'Sessão: ' . $sessao->titulo)

@section('content')
<div class="container py-4">

    {{-- Cabeçalho --}}
    <div class="card border-primary shadow-lg mb-4">
        <div class="card-header bg-primary bg-opacity-25 border-primary">
            <h2 class="fw-bold text-primary mb-0">🎲 {{ $sessao->titulo }}</h2>
        </div>
        <div class="card-body">
            <p class="mb-1">
                <strong>Campanha:</strong>
                <a href="{{ route('campanhas.show', $sessao->campanha->id) }}" class="text-decoration-none text-primary fw-semibold">
                    {{ $sessao->campanha->nome }}
                </a>
            </p>
            <p class="mb-1">
                <strong>Data e Hora:</strong>
                {{ \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}
            </p>
            <p class="mb-0">
                <strong>Status:</strong>
                @php
                    $cores = [
                        'agendada' => 'primary',
                        'em_andamento' => 'warning',
                        'concluida' => 'success',
                        'cancelada' => 'danger'
                    ];
                    $badgeCor = $cores[$sessao->status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $badgeCor }}">
                    {{ ucfirst(str_replace('_', ' ', $sessao->status)) }}
                </span>
            </p>
        </div>
    </div>

    {{-- Resumo --}}
    <div class="card border-secondary shadow-sm mb-4">
        <div class="card-header bg-secondary bg-opacity-25 border-secondary">
            <h4 class="fw-bold mb-0">📜 Resumo da Aventura</h4>
        </div>
        <div class="card-body">
            <p class="mb-0">
                {{ $sessao->resumo ?? 'Nenhum resumo detalhado foi fornecido para esta sessão.' }}
            </p>
        </div>
    </div>

    {{-- Personagens --}}
    <div class="card border-info shadow-sm mb-4">
        <div class="card-header bg-info bg-opacity-25 border-info d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0">🧙 Personagens Envolvidos</h4>

            {{-- Botão para jogador confirmar presença --}}
            @if(auth()->check() && auth()->user()->personagens->where('campanha_id', $sessao->campanha_id)->isNotEmpty())
                <form action="{{ route('sessoes.confirmar-personagem', $sessao->id) }}" method="POST" class="ms-3">
                    @csrf
                    <select name="personagem_id" class="form-select d-inline w-auto" required>
                        <option value="">Selecionar personagem</option>
                        @foreach(auth()->user()->personagens->where('campanha_id', $sessao->campanha_id) as $personagem)
                            <option value="{{ $personagem->id }}">{{ $personagem->nome }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-success btn-sm ms-1">
                        ✅ Confirmar Presença
                    </button>
                </form>
            @endif
        </div>

        <div class="card-body">
            @if($sessao->personagens->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach($sessao->personagens as $personagem)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <strong>{{ $personagem->nome }}</strong>
                                <small class="text-muted">
                                    — Jogador: {{ $personagem->usuario->name ?? 'Desconhecido' }}
                                </small>
                            </span>
                            @if(isset($personagem->pivot->presente))
                                <span class="badge bg-{{ $personagem->pivot->presente ? 'success' : 'secondary' }}">
                                    {{ $personagem->pivot->presente ? 'Presente' : 'Ausente' }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted mb-0">Nenhum personagem vinculado a esta sessão.</p>
            @endif
        </div>
    </div>

    {{-- Recompensas --}}
    <div class="card border-warning shadow-sm mb-4">
        <div class="card-header bg-warning bg-opacity-25 border-warning">
            <h4 class="fw-bold mb-0">💰 Recompensas Distribuídas</h4>
        </div>
        <div class="card-body">
            @if($sessao->xp_ganho || $sessao->ouro_ganho)
                <p class="mb-0">
                    Cada personagem recebeu
                    <strong class="text-primary">{{ $sessao->xp_ganho ?? 0 }} XP</strong>
                    e
                    <strong class="text-warning">{{ $sessao->ouro_ganho ?? 0 }} peças de ouro</strong>.
                </p>
            @else
                <p class="text-muted mb-0">Nenhuma recompensa registrada nesta sessão.</p>
            @endif
        </div>
    </div>

    {{-- Observações do Mestre --}}
    @if(!empty($sessao->notas))
        <div class="card border-light shadow-sm mb-4">
            <div class="card-header bg-light bg-opacity-25 border-light">
                <h4 class="fw-bold mb-0">🗒️ Observações do Mestre</h4>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $sessao->notas }}</p>
            </div>
        </div>
    @endif

    {{-- Rodapé --}}
    <div class="text-end mt-4 text-muted small">
        <p class="mb-1">
            Relatório criado pelo Mestre:
            <strong>{{ $sessao->mestre->name ?? 'Desconhecido' }}</strong>
        </p>
        <p class="mb-0">
            Criado em: {{ $sessao->created_at->format('d/m/Y H:i') }}
        </p>
    </div>

    {{-- Botões --}}
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('sessoes.index', ['campanha' => $sessao->campanha->id]) }}" class="btn btn-outline-secondary">
            ⬅️ Voltar
        </a>

        <div>
            @if(auth()->check() && (auth()->user()->id === $sessao->campanha->criador_id || auth()->user()->tipo === 'administrador'))
                <a href="{{ route('sessoes.edit', ['campanha' => $sessao->campanha->id, 'sessao' => $sessao->id]) }}"
                   class="btn btn-outline-warning me-2">
                    ✏️ Editar
                </a>
            @endif
            <a href="{{ route('sessoes.exportar-pdf', $sessao->id) }}" class="btn btn-primary">
                📄 Exportar PDF
            </a>
        </div>
    </div>
</div>
@endsection
