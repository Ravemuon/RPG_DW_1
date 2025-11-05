<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('personagens', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->integer('nivel')->default(1);
            $table->string('imagem')->nullable();
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('missao_id')->nullable()->constrained('missoes')->onDelete('set null');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personagems');
    }
};
