<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('personagem_pericias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personagem_id')->constrained('personagens')->onDelete('cascade');
            $table->foreignId('pericia_id')->constrained('pericias')->onDelete('cascade');
            $table->integer('valor')->nullable();
            $table->boolean('definida')->default(false); // true se definida manualmente
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('personagem_pericias');
    }
};
