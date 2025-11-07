<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('personagem_origem', function (Blueprint $table) {
        $table->id();
        $table->foreignId('personagem_id')->constrained('personagens')->onDelete('cascade');
        $table->foreignId('origem_id')->constrained('origens')->onDelete('cascade');
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('personagem_origem');
    }
};
