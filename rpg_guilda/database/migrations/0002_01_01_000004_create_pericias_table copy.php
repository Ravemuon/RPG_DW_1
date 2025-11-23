<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pericias', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
            $table->string('atributo_relacionado', 50)->nullable();
            $table->text('descricao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pericias');
    }
};
