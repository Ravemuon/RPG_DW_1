<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('tema', ['medieval', 'sobrenatural', 'cyberpunk'])->default('medieval');
            $table->enum('tipo', ['jogador', 'mestre', 'administrador'])->default('jogador');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
