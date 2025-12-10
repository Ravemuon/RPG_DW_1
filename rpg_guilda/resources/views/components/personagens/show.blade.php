@extends('layouts.app')

@section('title', 'Ficha de Personagem: ' . $personagem->nome)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            {{-- Mensagens de Sucesso/Erro --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-lg mb-4 bg-light">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">✨ {{ $personagem->nome }}</h2>
                    <div>
                        {{-- Botão de edição rápida --}}
                        <a href="{{ route('personagens.simpleEdit', $personagem) }}" class="btn btn-info btn-sm me-2">
                            <i class="fas fa-magic me-1"></i> Edição Rápida
                        </a>
                        {{-- Botão de listar personagens da campanha --}}
                        <a href="{{ route('personagens.index', ['campanha' => $personagem->campanha_id]) }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-list me-1"></i> Lista da Campanha
                        </a>
                        {{-- Botão de excluir personagem --}}
                        <form action="{{ route('personagens.destroy', $personagem) }}" method="POST" class="d-inline-block"
                              onsubmit="return confirm('Tem certeza que deseja excluir este personagem? Esta ação não pode ser desfeita.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-1"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    {{-- DADOS BÁSICOS & IMAGEM --}}
                    <div class="row mb-4 border-bottom pb-3">
                        <div class="col-md-3 text-center">
                            <img src="{{ $personagem->image_url ?? asset('storage/default/avatar.png') }}" 
                                 alt="Avatar de {{ $personagem->nome }}" 
                                 class="img-fluid rounded-circle shadow-sm" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                            <p class="mt-2 mb-0 text-muted">Ref. Página: {{ $personagem->pagina ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-9">
                            <dl class="row mb-0">
                                <dt class="col-sm-3">Campanha:</dt>
                                <dd class="col-sm-9">{{ $personagem->campanha?->nome ?? 'Nenhuma' }}</dd>

                                <dt class="col-sm-3">Sistema:</dt>
                                <dd class="col-sm-9">{{ $personagem->sistema?->nome ?? 'N/A' }}</dd>

                                <dt class="col-sm-3">Raça:</dt>
                                <dd class="col-sm-9">{{ $personagem->raca?->nome ?? 'N/A' }}</dd>

                                <dt class="col-sm-3">Classe:</dt>
                                <dd class="col-sm-9">{{ $personagem->classe?->nome ?? 'N/A' }}</dd>

                                <dt class="col-sm-3">Origem:</dt>
                                <dd class="col-sm-9">{{ $personagem->origem?->nome ?? 'N/A' }}</dd>

                                <dt class="col-sm-3">Nível:</dt>
                                <dd class="col-sm-3">{{ $personagem->nivel ?? 1 }}</dd>
                                
                                <dt class="col-sm-3">XP:</dt>
                                <dd class="col-sm-3">{{ $personagem->xp ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>

                    {{-- ATRIBUTOS --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-1">💪 Atributos</h5>
                            <ul class="list-group">
                                @foreach ($personagem->atributos ?? [] as $atributo => $valor) 
                                    <li class="list-group-item d-flex justify-content-between align-items-center text-capitalize">
                                        <strong class="me-3">{{ $atributo }}:</strong>
                                        <span class="badge bg-primary rounded-pill" style="min-width: 40px;">{{ $valor }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- RECURSOS --}}
                        <div class="col-md-6">
                            <h5 class="text-danger border-bottom pb-1">❤️ Recursos</h5>
                            @php
                                $recursos = [
                                    'vida' => ['label' => 'Vida (HP)', 'color' => 'danger', 'icon' => 'heart'],
                                    'sanidade' => ['label' => 'Sanidade', 'color' => 'info', 'icon' => 'brain'],
                                    'sorte' => ['label' => 'Sorte', 'color' => 'warning', 'icon' => 'dice'],
                                ];
                            @endphp
                            <ul class="list-group">
                                @foreach ($recursos as $campo => $config)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong><i class="fas fa-{{ $config['icon'] }} me-2"></i>{{ $config['label'] }}</strong>
                                        <span class="badge bg-{{ $config['color'] }} rounded-pill" style="min-width: 40px;">{{ $personagem->$campo ?? 0 }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- PERÍCIAS --}}
                    @if (!empty($personagem->pericias))
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-success border-bottom pb-1">🎯 Perícias</h5>
                            <div class="row">
                                @foreach ($personagem->pericias as $pericia => $bonus)
                                    <div class="col-md-4 mb-2">
                                        <span class="badge bg-success me-2">+{{ $bonus }}</span>
                                        <span class="text-capitalize">{{ $pericia }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- HISTÓRIA E PERSONALIDADE --}}
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <h5 class="text-secondary border-bottom pb-1">📜 História</h5>
                            <p class="text-break">{{ $personagem->historia ?? 'Nenhuma história registrada.' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5 class="text-secondary border-bottom pb-1">📝 Aparência e Personalidade</h5>
                            <p class="text-break"><strong>Aparência:</strong> {{ $personagem->descricao ?? 'N/A' }}</p>
                            <p class="text-break"><strong>Personalidade:</strong> {{ $personagem->personalidade ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- INVENTÁRIO & EQUIPAMENTO --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5 class="text-secondary border-bottom pb-1">🎒 Inventário</h5>
                            <pre class="bg-white p-3 border rounded text-break">{{ json_encode($personagem->inventario ?? [], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5 class="text-secondary border-bottom pb-1">🛡️ Equipamento</h5>
                            <pre class="bg-white p-3 border rounded text-break">{{ json_encode($personagem->equipamento ?? [], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('personagens.index', ['campanha' => $personagem->campanha_id]) }}" class="btn btn-secondary me-2">
                            <i class="fas fa-list me-1"></i> Voltar para Lista
                        </a>
                        <form action="{{ route('personagens.destroy', $personagem) }}" method="POST" class="d-inline-block"
                              onsubmit="return confirm('Tem certeza que deseja excluir este personagem?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Excluir Personagem
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
