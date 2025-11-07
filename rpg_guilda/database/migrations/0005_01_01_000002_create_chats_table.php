<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chats', function (Blueprint $table) {
            $table->id(); // PK padrão 'id'
            $table->foreignId('campanha_id')->constrained('campanhas')->onDelete('cascade');
            $table->string('nome')->default('Chat Principal');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('chats');
    }
};
