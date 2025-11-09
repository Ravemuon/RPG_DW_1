@php
    $complexidade = strtolower($sistema->complexidade ?? '');
    $complexidadeCor = match(true) {
        str_contains($complexidade, 'baixa') => 'bg-success text-white',
        str_contains($complexidade, 'média') => 'bg-warning text-dark',
        str_contains($complexidade, 'alta') => 'bg-danger text-white',
        default => 'bg-secondary text-white'
    };
@endphp

<div class="col-12 col-md-6 col-lg-4 d-flex align-items-stretch mb-4">
    <div class="card h-100 shadow-sm" style="background-color: var(--card-bg); border-color: var(--card-border);">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3">
            <h5 class="m-0 fw-bold text-truncate" style="font-size: 1.1rem;">{{ $sistema->nome }}</h5>
            <span class="badge bg-light text-primary fw-bold">#{{ $sistema->id }}</span>
        </div>

        <div class="card-body p-3 d-flex flex-column">
            <ul class="list-group list-group-flush small">
                <!-- Foco -->
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span><i class="bi bi-bullseye me-2 text-primary" aria-label="Ícone de foco"></i> Foco</span>
                    <strong>{{ $sistema->foco ?? '—' }}</strong>
                </li>
                <!-- Complexidade -->
                <li class="list-group-item d-flex justify-content-between align-items-center py-2 {{ $complexidadeCor }}">
                    <span><i class="bi bi-bar-chart me-2" aria-label="Ícone de complexidade"></i> Complexidade</span>
                    <strong>{{ $sistema->complexidade ?? '—' }}</strong>
                </li>
                <!-- Mecânica Principal -->
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span><i class="bi bi-dice-5 me-2 text-primary" aria-label="Ícone de mecânica"></i> Mecânica Principal</span>
                    <strong>{{ $sistema->mecanica_principal ?? '—' }}</strong>
                </li>
            </ul>

            <!-- Contagem dos Requisitos (Classes, Origens, Raças, Perícias) -->
            <div class="p-3">
                <div class="row row-cols-2 g-3 text-center">
                    <!-- Classes -->
                    <div class="col">
                        <a href="{{ route('sistemas.classes.index', $sistema) }}" class="text-decoration-none text-dark">
                            <span class="fw-bold fs-5">{{ $sistema->classes->count() }}</span><br>
                            <small class="text-muted">Classes</small>
                        </a>
                    </div>

                    <!-- Origens -->
                    <div class="col">
                        <a href="{{ route('sistemas.origens.index', $sistema) }}" class="text-decoration-none text-dark">
                            <span class="fw-bold fs-5">{{ $sistema->origens->count() }}</span><br>
                            <small class="text-muted">Origens</small>
                        </a>
                    </div>

                    <!-- Raças -->
                    <div class="col">
                        <a href="{{ route('sistemas.racas.index', $sistema) }}" class="text-decoration-none text-dark">
                            <span class="fw-bold fs-5">{{ $sistema->racas->count() }}</span><br>
                            <small class="text-muted">Raças</small>
                        </a>
                    </div>

                    <!-- Perícias -->
                    <div class="col">
                        <a href="{{ route('sistemas.pericias.index', $sistema) }}" class="text-decoration-none text-dark">
                            <span class="fw-bold fs-5">{{ $sistema->pericias->count() }}</span><br>
                            <small class="text-muted">Perícias</small>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="mt-auto border-top p-3 d-grid gap-2">
                <!-- Detalhes do Sistema -->
                <a href="{{ route('sistemas.show', $sistema) }}" class="btn btn-primary fw-semibold">
                    <i class="bi bi-eye me-1" aria-hidden="true"></i> Ver Detalhes
                </a>

                <!-- Link para Classes -->
                <a href="{{ route('sistemas.classes.index', $sistema->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list-columns-reverse me-1" aria-hidden="true"></i> Classes
                </a>
            </div>
        </div>
    </div>
</div>
