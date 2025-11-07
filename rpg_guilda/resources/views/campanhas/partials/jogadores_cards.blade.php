{{-- resources/views/campanhas/partials/card.blade.php --}}
@php
    // Padronizando variáveis
    $titulo = $titulo ?? 'Sem título';
    $descricao = $descricao ?? '';
    $borda = $borda ?? 'border-light';
    $acoes = $acoes ?? []; // Array de ações
@endphp

<div class="col-12 col-md-4">
    <div class="card bg-dark text-light {{ $borda }} shadow-sm p-3 h-100 d-flex flex-column justify-content-between">
        {{-- Título --}}
        <h5>{{ $titulo }}</h5>

        {{-- Descrição --}}
        @if($descricao)
            <p class="mb-2 small">{{ $descricao }}</p>
        @endif

        {{-- Ações --}}
        @if(count($acoes))
            <div class="d-flex gap-2 mt-auto flex-wrap">
                @foreach($acoes as $acao)
                    @if($acao['tipo'] === 'link')
                        <a href="{{ $acao['rota'] }}" class="btn {{ $acao['classe'] }}">{{ $acao['texto'] }}</a>
                    @elseif($acao['tipo'] === 'form')
                        <form action="{{ $acao['rota'] }}" method="POST" class="w-100"
                              @if(isset($acao['confirm'])) onsubmit="return confirm('{{ $acao['confirm'] }}')" @endif>
                            @csrf
                            @if(isset($acao['metodo']))
                                @method($acao['metodo'])
                            @endif
                            <button type="submit" class="btn {{ $acao['classe'] }} w-100">{{ $acao['texto'] }}</button>
                        </form>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
