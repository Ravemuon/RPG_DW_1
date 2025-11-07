{{-- resources/views/campanhas/partials/card.blade.php --}}
<div class="col-12 col-md-4">
    <div class="card bg-dark text-light {{ $borda ?? 'border-secondary' }} shadow-sm p-3 h-100">
        @if(isset($titulo))
            <h5>{{ $titulo }}</h5>
        @endif

        @if(isset($descricao))
            <p class="mb-1">{{ $descricao }}</p>
        @endif

        @if(isset($acoes) && count($acoes))
            <div class="d-flex gap-2 mt-2 flex-wrap">
                @foreach($acoes as $acao)
                    @if($acao['tipo'] === 'link')
                        <a href="{{ $acao['rota'] }}" class="btn {{ $acao['classe'] ?? 'btn-outline-light btn-sm' }}">
                            {{ $acao['texto'] ?? 'Ação' }}
                        </a>
                    @elseif($acao['tipo'] === 'form')
                        <form action="{{ $acao['rota'] }}" method="POST"
                              @if(isset($acao['confirm'])) onsubmit="return confirm('{{ $acao['confirm'] }}')" @endif
                              class="w-100">
                            @csrf
                            @if(isset($acao['metodo'])) @method($acao['metodo']) @endif
                            <button type="submit" class="btn {{ $acao['classe'] ?? 'btn-outline-light btn-sm' }} w-100">
                                {{ $acao['texto'] ?? 'Ação' }}
                            </button>
                        </form>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
