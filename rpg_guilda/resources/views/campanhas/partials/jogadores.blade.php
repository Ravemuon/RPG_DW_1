@php
    $mestres = $campanha->jogadores->where('pivot.status', 'mestre')->where('id', '!=', $campanha->criador_id);
    $ativos = $campanha->jogadores->where('pivot.status', 'ativo');
    $pendentes = $campanha->jogadores->where('pivot.status', 'pendente');
    $rejeitados = $campanha->jogadores->where('pivot.status', 'rejeitado');
@endphp

<div class="mb-5">
    {{-- Mestres --}}
    <h4 class="fw-bold text-warning mb-3">🧙 Mestres</h4>
    <div class="row g-3">
        {{-- Criador --}}
        @include('campanhas.partials.card', [
            'titulo' => $campanha->criador->nome ?? 'Desconhecido',
            'descricao' => $campanha->criador->personagens->count() ?
                '<ul class="mt-2 list-unstyled small">' .
                implode('', $campanha->criador->personagens->map(fn($p) => "<li>🎭 {$p->nome}</li>")->toArray()) .
                '</ul>'
                : 'Sem personagens',
            'borda' => 'border-success'
        ])

        {{-- Mestres adicionais --}}
        @foreach($mestres as $jogador)
            @include('campanhas.partials.card', [
                'titulo' => $jogador->nome,
                'descricao' => $jogador->personagens->count() ?
                    '<ul class="mt-2 list-unstyled small">' .
                    implode('', $jogador->personagens->map(fn($p) => "<li>🎭 {$p->nome}</li>")->toArray()) .
                    '</ul>'
                    : 'Sem personagens',
                'borda' => 'border-success'
            ])
        @endforeach
    </div>

    {{-- Jogadores ativos --}}
    <h4 class="fw-bold text-warning mt-4 mb-3">🧝 Jogadores Ativos</h4>
    <div class="row g-3">
        @foreach($ativos as $jogador)
            @include('campanhas.partials.card', [
                'titulo' => $jogador->nome,
                'descricao' => $jogador->personagens->count() ?
                    '<ul class="mt-2 list-unstyled small">' .
                    implode('', $jogador->personagens->map(fn($p) => "<li>🎭 {$p->nome}</li>")->toArray()) .
                    '</ul>'
                    : 'Sem personagens',
                'borda' => 'border-info'
            ])
        @endforeach
    </div>

    {{-- Jogadores pendentes --}}
    @if($pendentes->count())
        <h4 class="fw-bold text-warning mt-4 mb-3">⏳ Solicitações Pendentes</h4>
        <div class="row g-3">
            @foreach($pendentes as $jogador)
                @include('campanhas.partials.card', [
                    'titulo' => $jogador->nome,
                    'descricao' => '<strong>Status:</strong> Pendente',
                    'borda' => 'border-warning',
                    'acoes' => $isMestre ? [
                        [
                            'tipo' => 'form',
                            'rota' => route('campanhas.usuarios.gerenciar', $campanha->id),
                            'metodo' => 'POST',
                            'confirm' => null,
                            'inputs' => ['user_id' => $jogador->id, 'status' => 'ativo'],
                            'classe' => 'w-50',
                            'texto' => '✅ Aprovar'
                        ],
                        [
                            'tipo' => 'form',
                            'rota' => route('campanhas.usuarios.gerenciar', $campanha->id),
                            'metodo' => 'POST',
                            'confirm' => null,
                            'inputs' => ['user_id' => $jogador->id, 'status' => 'rejeitado'],
                            'classe' => 'w-50',
                            'texto' => '❌ Rejeitar'
                        ]
                    ] : []
                ])
            @endforeach
        </div>
    @endif

    {{-- Jogadores rejeitados --}}
    @if($rejeitados->count())
        <h4 class="fw-bold text-warning mt-4 mb-3">❌ Jogadores Rejeitados</h4>
        <div class="row g-3">
            @foreach($rejeitados as $jogador)
                @include('campanhas.partials.card', [
                    'titulo' => $jogador->nome,
                    'descricao' => 'Rejeitado',
                    'borda' => 'border-secondary'
                ])
            @endforeach
        </div>
    @endif
</div>
