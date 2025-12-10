@php
    // Define o estado de cada passo baseado nos dados do personagem ($data)
    $progress = [
        1 => ['title' => 'Dados Básicos', 'route' => 'personagens.create', 'status' => !empty($data['nome']) && !empty($data['campanha_id']), 'icon' => 'fa-address-card', 'color' => 'success'],
        2 => ['title' => 'Raça & Classe', 'route' => 'personagens.step2', 'status' => !empty($data['raca_id']) && !empty($data['classe_id']), 'icon' => 'fa-scroll', 'color' => 'warning'],
        3 => ['title' => 'Atributos', 'route' => 'personagens.step3', 'status' => !empty($data['atributos']), 'icon' => 'fa-wand-magic-sparkles', 'color' => 'primary'],
        4 => ['title' => 'Vida & Recursos', 'route' => 'personagens.step4', 'status' => !empty($data['vida']), 'icon' => 'fa-shield-heart', 'color' => 'danger'],
        5 => ['title' => 'Perícias & Inv.', 'route' => 'personagens.step5', 'status' => !empty($data['pericias']) && !empty($data['inventario']), 'icon' => 'fa-sack-dollar', 'color' => 'secondary'],
        6 => ['title' => 'Finalizar', 'route' => 'personagens.final', 'status' => false, 'icon' => 'fa-crown', 'color' => 'dark'],
    ];

    // 1. Determina o passo atual (ajustado para ser mais limpo)
    $routeName = Route::currentRouteName();
    if ($routeName === 'personagens.create') $currentStep = 1;
    elseif ($routeName === 'personagens.final') $currentStep = 6;
    else $currentStep = (int) filter_var($routeName, FILTER_SANITIZE_NUMBER_INT);

    // 2. Verifica a conclusão total para o Passo 6 (Finalizar)
    $isFullyComplete = array_reduce(array_slice($progress, 0, 5, true), function ($carry, $item) {
        return $carry && $item['status'];
    }, true);
    $progress[6]['status'] = $isFullyComplete; // Atualiza o status do passo 'Finalizar'
@endphp

<div class="card shadow mb-4 border-start border-4 border-primary">
    <div class="card-header bg-primary text-white p-3">
        <h5 class="mb-0 fs-5 d-flex align-items-center">
            <i class="fas fa-fw fa-map-signs me-2"></i>
            Progresso de Criação
        </h5>
        <small class="text-white-50">{{ $data['nome'] ?? 'Novo Personagem' }}</small>
    </div>
    <div class="list-group list-group-flush">
        @foreach ($progress as $stepNumber => $step)
            @php
                // Determina se o passo anterior foi completado (para acesso sequencial)
                $previousComplete = $stepNumber > 1 ? $progress[$stepNumber - 1]['status'] : true;
                
                // Regra de acesso: Está acessando o passo atual OU o passo anterior está completo.
                // O passo 6 tem uma regra especial de acesso total (já definida em $isFullyComplete)
                if ($stepNumber === 6) {
                    $canAccess = $isFullyComplete;
                } else {
                    $canAccess = $stepNumber <= $currentStep || $previousComplete;
                }

                $linkUrl = $canAccess ? route($step['route'], ['campanha' => $data['campanha_id'] ?? null]) : '#';
                $isDisabled = !$canAccess;

                // Classes de estilo
                $itemClasses = [];
                if ($stepNumber === $currentStep) {
                    $itemClasses[] = 'active'; // Item atualmente sendo editado
                    $statusIcon = 'fa-pen-to-square';
                    $statusText = 'Editando...';
                    $statusColor = 'bg-secondary';
                } elseif ($step['status']) {
                    $itemClasses[] = 'list-group-item-success text-success fw-bold'; // Passo completo
                    $statusIcon = 'fa-check';
                    $statusText = 'Completo';
                    $statusColor = 'bg-success';
                } else {
                    // Passo futuro ou passo anterior incompleto/bloqueado
                    $itemClasses[] = $isDisabled ? 'text-muted' : '';
                    $statusIcon = $isDisabled ? 'fa-lock' : 'fa-clock';
                    $statusText = $isDisabled ? 'Bloqueado' : 'Pendente';
                    $statusColor = $isDisabled ? 'bg-secondary' : 'bg-warning';
                }
            @endphp

            <a href="{{ $linkUrl }}"
                class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center {{ implode(' ', $itemClasses) }} {{ $isDisabled ? 'disabled' : '' }}"
                aria-disabled="{{ $isDisabled ? 'true' : 'false' }}">

                <div class="d-flex align-items-center">
                    <i class="fas fa-fw {{ $step['icon'] }} fa-lg me-3"></i>
                    <div>
                        <strong class="d-block">Passo {{ $stepNumber }}: {{ $step['title'] }}</strong>
                    </div>
                </div>

                <span class="badge {{ $statusColor }} text-white rounded-pill p-2">
                    <i class="fas fa-fw {{ $statusIcon }}"></i> 
                    <span class="d-none d-md-inline">{{ $statusText }}</span>
                </span>
            </a>
        @endforeach
    </div>
</div>