<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('personagem_pericia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personagem_id')->constrained('personagens')->onDelete('cascade');
            $table->foreignId('pericia_id')->constrained('pericias')->onDelete('cascade');
            $table->tinyInteger('nivel')->default(1);
            $table->boolean('proficiente')->default(false);
            $table->timestamps();

            $table->unique(['personagem_id', 'pericia_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('personagem_pericia');
    }
};
