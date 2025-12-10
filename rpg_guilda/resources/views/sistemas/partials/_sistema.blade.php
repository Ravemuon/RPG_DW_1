@php
    $complexidade = strtolower($sistema->complexidade ?? '');
    $complexidadeCor = match(true) {
        str_contains($complexidade, 'baixa') => 'bg-success text-white',
        str_contains($complexidade, 'média') => 'bg-warning text-dark',
        str_contains($complexidade, 'alta') => 'bg-danger text-white',
        default => 'bg-secondary text-white'
    };

    $classesCount   = $sistema->classes->count() ?? 0;
    $origensCount   = $sistema->origens->count() ?? 0;
    $racasCount     = $sistema->racas->count() ?? 0;
    $periciasCount  = $sistema->pericias->count() ?? 0;
@endphp

<div class="col-12 col-md-6 col-lg-4 d-flex align-items-stretch mb-4">
    <div class="card h-100 shadow-sm" style="background-color: var(--card-bg); border-color: var(--card-border);">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3">
            <h5 class="m-0 fw-bold text-truncate" style="font-size: 1.1rem;">{{ $sistema->nome }}</h5>
            <span class="badge bg-light text-primary fw-bold">#{{ $sistema->id }}</span>
        </div>

        <div class="card-body p-3 d-flex flex-column">
            <ul class="list-group list-group-flush small mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span><i class="bi bi-bullseye me-2 text-primary"></i> Foco</span>
                    <strong>{{ $sistema->foco ?? '—' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-2 {{ $complexidadeCor }}">
                    <span><i class="bi bi-bar-chart me-2"></i> Complexidade</span>
                    <strong>{{ $sistema->complexidade ?? '—' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span><i class="bi bi-dice-5 me-2 text-primary"></i> Mecânica Principal</span>
                    <strong>{{ $sistema->mecanica_principal ?? '—' }}</strong>
                </li>
            </ul>

            <div class="p-3">
                <div class="row row-cols-2 g-3 text-center">
                    <div class="col">
                        <span class="fw-bold fs-5">{{ $classesCount }}</span><br>
                        <small class="text-muted">Classes</small>
                    </div>
                    <div class="col">
                        <span class="fw-bold fs-5">{{ $origensCount }}</span><br>
                        <small class="text-muted">Origens</small>
                    </div>
                    <div class="col">
                        <span class="fw-bold fs-5">{{ $racasCount }}</span><br>
                        <small class="text-muted">Raças</small>
                    </div>
                    <div class="col">
                        <span class="fw-bold fs-5">{{ $periciasCount }}</span><br>
                        <small class="text-muted">Perícias</small>
                    </div>
                </div>
            </div>

            <div class="mt-auto pt-3">
                <a href="{{ route('sistemas.show', $sistema->id) }}" class="btn btn-primary w-100">
                    <i class="bi bi-eye me-1"></i> Ver Sistema
                </a>
            </div>
        </div>
    </div>
</div>
