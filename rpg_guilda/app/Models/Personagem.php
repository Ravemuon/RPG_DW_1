<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage; // Importação necessária para gerar a URL da imagem

class Personagem extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao Model.
     *
     * @var string
     */
    protected $table = 'personagens';

    /**
     * Os atributos que podem ser atribuídos em massa (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'user_id',
        'campanha_id',
        'raca_id',
        'classe_id',
        'origem_id',
        'sistema_id',
        'nivel',
        'xp',
        'bonus_proficiencia',
        'sanidade',
        'sorte',
        'atributos',
        'descricao',
        'historia',
        'personalidade',
        'inventario',
        'imagem', // Mantido para mass assignment
        'ativo',
        'pagina',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'atributos' => 'array', // Converte o campo JSON 'atributos' para array/objeto PHP
        'ativo' => 'boolean',
    ];

    // --- Accessors (Acessadores) ---

    /**
     * Obtém a URL pública completa para a imagem do personagem.
     * Acessível como $personagem->image_url.
     *
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        // 'imagem' armazena o path relativo dentro do disco 'public'
        if ($this->imagem) {
            // Usa o Storage facade para gerar a URL pública
            return Storage::url($this->imagem);
        }

        // Retorna null ou um caminho para uma imagem placeholder padrão
        return null;
        /* Exemplo de placeholder:
        return asset('img/placeholders/default-personagem.png'); */
    }

    // --- Relacionamentos ---

    /**
     * Obtém o usuário proprietário do personagem.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém a campanha a qual o personagem pertence.
     */
    public function campanha(): BelongsTo
    {
        return $this->belongsTo(Campanha::class);
    }

    /**
     * Obtém a raça do personagem.
     */
    public function raca(): BelongsTo
    {
        return $this->belongsTo(Raca::class);
    }

    /**
     * Obtém a classe do personagem.
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Obtém a origem (background) do personagem.
     */
    public function origem(): BelongsTo
    {
        return $this->belongsTo(Origem::class);
    }

    /**
     * Obtém o sistema de regras do personagem.
     */
    public function sistema(): BelongsTo
    {
        return $this->belongsTo(Sistema::class);
    }
}
