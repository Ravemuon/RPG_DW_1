{{-- View parcial: personagens/create/_dashboard_preview.blade.php --}}

<div class="mb-3">
    <h6 class="text-primary border-bottom pb-1">👤 Dados Essenciais</h6>
    <ul class="list-unstyled small">
        <li><strong>Nome:</strong> <span id="dashboard-nome">{{ $data['nome'] ?? 'Aguardando...' }}</span></li>
        <li><strong>Raça:</strong> <span id="dashboard-raca">
            {{ optional(App\Models\Raca::find($data['raca_id'] ?? null))->nome ?? 'N/A' }}
        </span></li>
        <li><strong>Classe:</strong> <span id="dashboard-classe">
            {{ optional(App\Models\Classe::find($data['classe_id'] ?? null))->nome ?? 'N/A' }}
        </span></li>
        <li><strong>Nível:</strong> {{ $data['nivel'] ?? 1 }}</li>
    </ul>
</div>

<div class="mb-3">
    <h6 class="text-primary border-bottom pb-1">💪 Atributos (P3)</h6>
    <div class="row row-cols-2 g-1 small">
        @foreach ($atributosPadrao as $atributo)
            @php
                $valor = $atributosAtuais[$atributo] ?? 10;
                $mod = floor(($valor - 10) / 2);
                $mod_display = ($mod > 0 ? '+' : '') . $mod;
            @endphp
            <div class="col">
                <strong>{{ ucfirst(substr($atributo, 0, 3)) }}:</strong> 
                <span id="dashboard-attr-{{ $atributo }}" class="fw-bold text-success">{{ $valor }}</span> 
                (<span id="dashboard-mod-{{ $atributo }}">{{ $mod_display }}</span>)
            </div>
        @endforeach
    </div>
</div>

<div class="mb-3">
    <h6 class="text-primary border-bottom pb-1">❤️ Recursos (P4+)</h6>
    <ul class="list-unstyled small">
        <li><strong>Vida (PV):</strong> {{ $data['vida'] ?? '?' }}</li>
        <li><strong>Sanidade:</strong> {{ $data['sanidade'] ?? '?' }}</li>
        <li><strong>Próximo Passo:</strong> Perícias (P4)</li>
    </ul>
</div>
