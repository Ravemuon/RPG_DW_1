<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Só cria se ainda não existir
        if (!Schema::hasTable('sistema_pericia')) {
            Schema::create('sistema_pericia', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
                $table->foreignId('pericia_id')->constrained('pericias')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['sistema_id','pericia_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_pericia');
    }
};
