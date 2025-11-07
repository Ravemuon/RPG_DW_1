<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('sistema_raca', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
        $table->foreignId('raca_id')->constrained('racas')->onDelete('cascade');
        $table->timestamps();
        $table->unique(['sistema_id','raca_id']);
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('sistema_raca');
    }
};
