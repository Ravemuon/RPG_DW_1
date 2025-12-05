@php
    use Illuminate\Support\Str;

    /** =================== VALORES PADRÃO =================== */
    $lista         = collect($items ?? []); // garante collection
    $total         = $lista->count();
    $limit         = $limit ?? 5;
    $title         = $title ?? 'Seção';
    $defaultIcon   = '📦'; // Emoji padrão substituindo 'bi-collection'
    $icon          = $icon ?? $defaultIcon; // O ícone/emoji principal é passado como $icon
    $subtitle      = $subtitle ?? 'Itens relacionados ao sistema';
    $route         = $route ?? null;
    $addRoute      = $addRoute ?? null;
    $emptyMessage  = $emptyMessage ?? 'Nenhum registro disponível.';

    $color         = $color ?? 'info';
    $colorClasses = [
        'info'    => ['bg'=>'bg-info',    'text'=>'text-info'],
        'primary' => ['bg'=>'bg-primary', 'text'=>'text-primary'],
        'success' => ['bg'=>'bg-success', 'text'=>'text-success'],
        'warning' => ['bg'=>'bg-warning', 'text'=>'text-warning'],
        'danger'  => ['bg'=>'bg-danger',  'text'=>'text-danger'],
        'purple'  => ['bg'=>'bg-purple',  'text'=>'text-purple'],
        'indigo'  => ['bg'=>'bg-indigo',  'text'=>'text-indigo'],
    ];
    $selectedColor = $colorClasses[$color] ?? $colorClasses['info'];

    /** Emojis */
    $emojiMap = [
        'default' => '📦',
        'ver_todos' => '➡️',
        'vazio' => '📥',
        'adicionar' => '➕',
        'numero_item' => '🔢',
        'calendario' => '📅',
        'atualizado' => '🔄',
        'detalhes' => '👁️',
        'ativo' => '🟢',
        'inativo' => '⚫',
        'lista' => '📃',
        // Adicione mais mapeamentos se $icon puder receber outros nomes de bi-
    ];

    $headerEmoji = $emojiMap[$icon] ?? $icon; // Tenta usar o $icon se já for um emoji, senão usa o padrão
@endphp

<div class="card border-0 shadow-sm hover-lift mb-4 overflow-hidden">

    {{-- =================== HEADER =================== --}}
    <div class="card-header {{ $selectedColor['bg'] }} bg-gradient text-white border-0 position-relative"
        style="border-radius: 12px 12px 0 0 !important;">
        <div class="position-absolute top-0 end-0 opacity-10" style="width:100px;height:100px; font-size: 5rem;">
            {{ $headerEmoji }}
        </div>

        <div class="d-flex justify-content-between align-items-center position-relative">
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 rounded-circle bg-white bg-opacity-25" style="font-size: 1.5rem;">
                    {{ $headerEmoji }}
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ $title }}</h5>
                    <small class="opacity-75 d-block">{{ $subtitle }}</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white {{ $selectedColor['text'] }} fw-bold px-3 py-2">{{ $total }}</span>
                @if(!empty($route) && $total > 0)
                    <a href="{{ $route }}" class="btn btn-sm btn-light rounded-circle p-1" data-bs-toggle="tooltip" title="Ver todos" style="font-size: 1rem;">
                        {{ $emojiMap['ver_todos'] }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- =================== BODY =================== --}}
    <div class="card-body p-0">

        {{-- -------- Vazio -------- --}}
        @if($total === 0)
            <div class="text-center py-5">
                <span style="font-size: 3rem;" class="{{ $selectedColor['text'] }} opacity-50 d-block mb-3">{{ $emojiMap['vazio'] }}</span>
                <p class="text-muted">{{ $emptyMessage }}</p>

                @if($addRoute)
                    <a href="{{ $addRoute }}" class="btn btn-sm {{ $selectedColor['bg'] }} text-white mt-2 shadow-sm">
                        <span class="me-1">{{ $emojiMap['adicionar'] }}</span> Adicionar novo
                    </a>
                @endif
            </div>

        {{-- -------- Lista de Itens -------- --}}
        @else
            <div class="list-group list-group-flush">
                @foreach($lista->take($limit)->values() as $index => $item)
                    {{-- Adiciona classe hover específica para o item da lista --}}
                    <div class="list-group-item border-0 px-4 py-3 {{ $index % 2 === 0 ? 'bg-light' : '' }} hover-bg-{{ $color }}-light">
                        <div class="d-flex justify-content-between align-items-start">

                            {{-- Coluna Esquerda --}}
                            <div class="flex-grow-1 me-3">
                                {{-- Nome e tipo --}}
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-light text-dark me-2">{{ $emojiMap['numero_item'] }} {{ $index+1 }}</span>
                                    <h6 class="fw-bold mb-0">
                                        {{ is_object($item) ? ($item->nome ?? $item) : $item }}
                                        @if(is_object($item) && isset($item->tipo))
                                            <span class="badge bg-light border text-dark ms-2">{{ $item->tipo }}</span>
                                        @endif
                                    </h6>
                                </div>

                                {{-- Descrição --}}
                                @if(is_object($item) && isset($item->descricao))
                                    <p class="text-muted small mb-2">{{ Str::limit($item->descricao,120) }}</p>
                                @endif

                                {{-- Metadados --}}
                                <small class="text-muted d-flex gap-3">
                                    @if(is_object($item) && isset($item->created_at))
                                        <span>{{ $emojiMap['calendario'] }} {{ $item->created_at->format('d/m/Y') }}</span>
                                    @endif
                                    @if(is_object($item) && isset($item->updated_at) && $item->updated_at->ne($item->created_at))
                                        <span>{{ $emojiMap['atualizado'] }} Atualizado {{ $item->updated_at->diffForHumans() }}</span>
                                    @endif
                                </small>
                            </div>

                            {{-- Ações --}}
                            <div class="d-flex align-items-center gap-2">
                                @if(is_object($item) && isset($item->id))
                                    @php
                                        // Detecta rota show correta
                                        $modelName = strtolower(class_basename($item));
                                        $routeName = $modelName . 's.show';
                                        $itemRoute = Route::has($routeName)
                                            ? route($routeName, array_merge(
                                                ['sistema'=>request()->route('sistema') ?? null],
                                                [$modelName=>$item->id]
                                              ))
                                            : '#';
                                    @endphp

                                    {{-- Botão Ver Detalhes com texto e estilo btn-sm --}}
                                    <a href="{{ $itemRoute }}"
                                       class="btn btn-sm btn-outline-{{ $color }}"
                                       title="Ver detalhes"
                                       data-bs-toggle="tooltip">
                                       <span class="me-1">{{ $emojiMap['detalhes'] }}</span> Ver
                                    </a>
                                @endif

                                @if(is_object($item) && isset($item->ativo))
                                    <span class="badge rounded-pill {{ $item->ativo?$selectedColor['bg']:'bg-secondary' }} text-white">
                                        {{ $item->ativo ? $emojiMap['ativo'] . ' Ativo' : $emojiMap['inativo'] . ' Inativo' }}
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- -------- Footer / Ver mais -------- --}}
            @if($route)
                <div class="card-footer bg-transparent px-4 pb-4">
                    @if($total > $limit)
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Mostrando **{{ $limit }}** de **{{ $total }}** itens</small>
                            <a href="{{ $route }}" class="btn btn-sm {{ $selectedColor['bg'] }} text-white rounded-pill px-4 shadow-sm">
                                Ver todos <span class="ms-1">{{ $emojiMap['ver_todos'] }}</span>
                            </a>
                        </div>
                    @else
                        <div class="text-center">
                            <a href="{{ $route }}" class="btn btn-sm btn-outline-{{ $color }} rounded-pill px-4">
                                <span class="me-1">{{ $emojiMap['lista'] }}</span> Lista completa
                            </a>
                        </div>
                    @endif
                </div>
            @endif

        @endif
    </div>
</div>

{{-- =================== Estilos específicos =================== --}}
<style>
    /* Estilos para o card */
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-4px); box-shadow:0 10px 25px rgba(0,0,0,0.1) !important; }

    /* Cores de fundo claro ao passar o mouse sobre os itens da lista */
    .hover-bg-info-light:hover    { background-color: rgba(13,202,240,0.05) !important; }
    .hover-bg-primary-light:hover { background-color: rgba(13,110,253,0.05) !important; }
    .hover-bg-success-light:hover { background-color: rgba(25,135,84,0.05) !important; }
    .hover-bg-warning-light:hover { background-color: rgba(255,193,7,0.05) !important; }
    .hover-bg-danger-light:hover  { background-color: rgba(220,53,69,0.05) !important; }
    .hover-bg-purple-light:hover  { background-color: rgba(111,66,193,0.05) !important; }
    .hover-bg-indigo-light:hover  { background-color: rgba(102,16,242,0.05) !important; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa Tooltips (se o Bootstrap JS estiver carregado)
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el=>{
                new bootstrap.Tooltip(el);
            });
        }
    });
</script>
@endpush