<div class="card shadow-lg border-0 rounded-4">
    <div class="card-body p-0">
        @if($sistemas->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inboxes text-muted display-4"></i>
                <p class="mt-3 fs-5 text-muted">Nenhum sistema cadastrado.</p>
                <a href="{{ route('sistemas.create') }}" class="btn btn-primary rounded-pill mt-2">
                    <i class="bi bi-plus-circle me-1"></i> Adicionar Novo Sistema
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>#ID</th>
                            <th class="text-start">Nome do Sistema</th>
                            <th>Complexidade</th>
                            <th>Foco Principal</th>
                            <th>Mecânica Chave</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sistemas as $sistema)
                            @php
                                $complexidade = strtolower($sistema->complexidade ?? '');
                                $complexidadeData = match(true) {
                                    str_contains($complexidade, 'baixa') => ['bg' => 'bg-success', 'text' => 'Baixa', 'icon' => 'bi-check-circle-fill'],
                                    str_contains($complexidade, 'média'), str_contains($complexidade, 'media') => ['bg' => 'bg-warning text-dark', 'text' => 'Média', 'icon' => 'bi-exclamation-triangle-fill'],
                                    str_contains($complexidade, 'alta') => ['bg' => 'bg-danger', 'text' => 'Alta', 'icon' => 'bi-fire'],
                                    default => ['bg' => 'bg-secondary', 'text' => '—', 'icon' => 'bi-question-circle-fill']
                                };
                            @endphp
                            <tr class="table-row-hover align-middle text-center">
                                {{-- ID --}}
                                <td class="fw-bold text-primary">#{{ $sistema->id }}</td>

                                {{-- Nome --}}
                                <td class="text-start text-truncate" style="max-width: 220px;">{{ $sistema->nome }}</td>

                                {{-- Complexidade --}}
                                <td>
                                    <span class="badge {{ $complexidadeData['bg'] }} px-3 py-2 rounded-pill shadow-sm">
                                        <i class="bi {{ $complexidadeData['icon'] }} me-1"></i>
                                        {{ $complexidadeData['text'] }}
                                    </span>
                                </td>

                                {{-- Foco --}}
                                <td>{{ $sistema->foco ?? '—' }}</td>

                                {{-- Mecânica --}}
                                <td class="text-truncate" style="max-width: 180px;">{{ $sistema->mecanica_principal ?? '—' }}</td>

                                {{-- Ações --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        {{-- Ver --}}
                                        <a href="{{ route('sistemas.show', $sistema) }}"
                                           class="btn btn-sm btn-outline-info"
                                           data-bs-toggle="tooltip"
                                           title="Detalhes">
                                           <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- Editar --}}
                                        <a href="{{ route('sistemas.edit', $sistema) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           data-bs-toggle="tooltip"
                                           title="Editar">
                                           <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- Excluir --}}
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal-{{ $sistema->id }}"
                                                title="Excluir">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal de Exclusão --}}
                            <div class="modal fade" id="deleteModal-{{ $sistema->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $sistema->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="deleteModalLabel-{{ $sistema->id }}">Confirmação de Exclusão</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja <strong>excluir permanentemente</strong> o sistema: <strong>{{ $sistema->nome }}</strong>? Esta ação não pode ser desfeita.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('sistemas.destroy', $sistema) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Sim, Excluir!</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            @if($sistemas->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $sistemas->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tooltip Bootstrap
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
    });
</script>
@endpush
