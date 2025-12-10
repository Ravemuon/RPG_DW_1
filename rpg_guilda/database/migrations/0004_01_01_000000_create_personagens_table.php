<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('personagens', function (Blueprint $table) {
            $table->id();

            // Dados básicos    
            $table->string('nome', 100);
            $table->string('apelido', 100)->nullable()->comment('Apelido ou alcunha do personagem');

            // Chaves estrangeiras principais
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Jogador dono do personagem');
                  
            $table->foreignId('campanha_id')
                  ->constrained('campanhas')
                  ->onDelete('cascade')
                  ->comment('Campanha onde o personagem está');

            // Chaves estrangeiras opcionais
            $table->foreignId('raca_id')
                  ->nullable()
                  ->constrained('racas')
                  ->nullOnDelete()
                  ->comment('Raça do personagem');
                  
            $table->foreignId('classe_id')
                  ->nullable()
                  ->constrained('classes')
                  ->nullOnDelete()
                  ->comment('Classe do personagem');
                  
            $table->foreignId('origem_id')
                  ->nullable()
                  ->constrained('origens')
                  ->nullOnDelete()
                  ->comment('Origem ou antecedente do personagem');
                  
            $table->foreignId('sistema_id')
                  ->nullable()
                  ->constrained('sistemas')
                  ->nullOnDelete()
                  ->comment('Sistema de RPG (herdado da campanha)');

            // Sistema de nível e experiência
            $table->integer('nivel')->default(1)->comment('Nível atual do personagem');
            $table->integer('xp')->default(0)->comment('Experiência acumulada');
            $table->integer('bonus_proficiencia')->default(2)->comment('Bônus de proficiência baseado no nível');
            $table->integer('xp_proximo_nivel')->nullable()->comment('XP necessário para próximo nível');

            // Pontos de vida e recursos
            $table->integer('pontos_vida_max')->nullable()->comment('Pontos de vida máximos');
            $table->integer('pontos_vida_atual')->nullable()->comment('Pontos de vida atuais');
            $table->integer('pontos_vida_temporario')->nullable()->comment('Pontos de vida temporários');
            
            // Recursos de classe (como magia, ki, etc.)
            $table->json('recursos_classe')->nullable()->comment('Recursos específicos da classe (mana, ki, etc.)');

            // Atributos especiais
            $table->integer('sanidade')->nullable()->comment('Pontuação de sanidade mental');
            $table->integer('sorte')->nullable()->comment('Pontuação de sorte ou destino');
            $table->integer('inspiracao')->default(0)->comment('Pontos de inspiração');

            // Atributos principais (armazenados como JSON para flexibilidade)
            $table->json('atributos')->nullable()->comment('Valores de atributos (força, destreza, etc.)');
            
            // Perícias e talentos
            $table->json('pericias_selecionadas')->nullable()->comment('Perícias em que o personagem é proficiente');
            $table->json('talentos')->nullable()->comment('Talentos ou feats do personagem');
            
            // Defesas e resistências
            $table->integer('classe_armadura')->nullable()->comment('Classe de armadura');
            $table->integer('iniciativa')->nullable()->comment('Modificador de iniciativa');
            $table->json('resistencia_morte')->nullable()->comment('Testes de resistência à morte');
            $table->json('resistencia_magia')->nullable()->comment('Testes de resistência à magia');

            // Informações detalhadas do personagem
            $table->text('descricao')->nullable()->comment('Descrição física e aparência');
            $table->text('historia')->nullable()->comment('História de fundo');
            $table->text('personalidade')->nullable()->comment('Traços de personalidade');
            $table->text('objetivos')->nullable()->comment('Objetivos e motivações');
            $table->text('conexoes')->nullable()->comment('Conexões com outros personagens ou NPCs');
            
            // Inventário e equipamento
            $table->json('inventario')->nullable()->comment('Itens do inventário');
            $table->json('equipamento')->nullable()->comment('Equipamento sendo usado');
            $table->integer('carga_maxima')->nullable()->comment('Carga máxima em peso');
            $table->integer('carga_atual')->nullable()->comment('Carga atual em peso');
            $table->integer('dinheiro')->default(0)->comment('Dinheiro/riqueza do personagem');

            // Magias e habilidades
            $table->json('magias_conhecidas')->nullable()->comment('Magias conhecidas ou preparadas');
            $table->json('habilidades_especiais')->nullable()->comment('Habilidades especiais da classe/raça');
            
            // Imagem e mídia
            $table->string('imagem')->nullable()->comment('URL da imagem do personagem');
            $table->string('token')->nullable()->comment('Token para VTT (Virtual Table Top)');
            
            // Configurações e status
            $table->boolean('ativo')->default(true)->comment('Se o personagem está ativo na campanha');
            $table->boolean('favorito')->default(false)->comment('Se é um personagem favorito do jogador');
            $table->enum('status', ['vivo', 'morto', 'inativo', 'aposentado'])->default('vivo');
            
            // Localização e contexto
            $table->string('localizacao')->nullable()->comment('Localização atual na campanha');
            $table->string('pagina', 100)->nullable()->comment('Página de referência ou link');
            
            // Metadados
            $table->integer('versao_ficha')->default(1)->comment('Versão da ficha para controle de mudanças');
            $table->json('configuracoes')->nullable()->comment('Configurações personalizadas da ficha');
            $table->json('notas_privadas')->nullable()->comment('Notas privadas do jogador');
            $table->json('notas_mestre')->nullable()->comment('Notas visíveis apenas para o mestre');

            $table->timestamps();
            $table->softDeletes()->comment('Para possibilitar restauração do personagem');

            // Indexes otimizados
            $table->index('nome');
            $table->index('user_id');
            $table->index('campanha_id');
            $table->index('sistema_id');
            $table->index('status');
            $table->index('ativo');
            $table->index('favorito');
            $table->index('nivel');
            $table->index(['user_id', 'campanha_id']);
            $table->index(['nome', 'raca_id', 'classe_id']);
            $table->index(['nivel', 'xp', 'status']);
            $table->index(['created_at', 'updated_at']);
            
            // Indexes compostos para buscas frequentes
            $table->index(['user_id', 'ativo', 'favorito']);
            $table->index(['campanha_id', 'status', 'ativo']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('personagens');
    }
};