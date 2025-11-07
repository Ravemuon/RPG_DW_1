<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sistema_pericias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
            $table->foreignId('pericia_id')->constrained('pericias')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['sistema_id', 'pericia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sistema_pericias');
    }
};
