<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sistema_classe', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sistema_id')
                  ->constrained('sistemas')
                  ->onDelete('cascade')
                  ->comment('Referência ao sistema');

            $table->foreignId('classe_id')
                  ->constrained('classes')
                  ->onDelete('cascade')
                  ->comment('Referência à classe');

            $table->timestamps();

            $table->unique(['sistema_id', 'classe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sistema_classe');
    }
};
