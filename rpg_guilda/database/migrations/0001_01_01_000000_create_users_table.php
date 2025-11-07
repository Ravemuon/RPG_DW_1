<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('username', 50)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable()->default('/imagens/default/avatar.png');
            $table->string('banner')->nullable()->default('/imagens/default/banner.png');
            $table->enum('tema', [
                'medieval','fantasia','sobrenatural','steampunk','cyberpunk','apocaliptico','oceano','floresta','deserto'
            ])->default('medieval');
            $table->enum('papel',['jogador','mestre','administrador'])->default('jogador');
            $table->string('pagina', 50)->nullable()->comment('Página do usuário ou referência');
            $table->rememberToken();
            $table->timestamps();
            $table->index(['nome', 'username']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
