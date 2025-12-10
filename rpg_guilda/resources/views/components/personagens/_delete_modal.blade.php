{{-- Este arquivo deve ser incluído no index.blade.php para cada personagem --}}
<div class="modal fade" id="deleteModal-{{ $personagem->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $personagem->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel-{{ $personagem->id }}">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Você tem certeza que deseja deletar permanentemente o personagem <strong>{{ $personagem->nome }}</strong>? Esta ação não pode ser desfeita.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('personagens.destroy', $personagem) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Deletar</button>
                </form>
            </div>
        </div>
    </div>
</div>