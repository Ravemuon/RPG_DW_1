<div class="card mb-3">
    <div class="card-header bg-info text-white">
        <h5 class="m-0">Perícias</h5>
    </div>
    <div class="card-body">
        @if($sistema->pericias->isEmpty())
            <p>Não há perícias configuradas para este sistema.</p>
        @else
            <ul class="list-group">
                @foreach($sistema->pericias as $pericia)
                    <li class="list-group-item">
                        <strong>{{ $pericia->nome }}</strong> - {{ $pericia->descricao ?? 'Descrição não disponível' }}
                    </li>
                @endforeach
            </ul>
        @endif
        <a href="{{ route('sistemas.pericias.create', $sistema->id) }}" class="btn btn-link">Nova Perícia</a>
    </div>
</div>
