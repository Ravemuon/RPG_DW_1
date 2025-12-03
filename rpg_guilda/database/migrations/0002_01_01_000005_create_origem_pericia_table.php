<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('origem_pericia', function (Blueprint $table) {
            $table->id();

            $table->foreignId('origem_id')
                ->constrained('origens')
                ->onDelete('cascade');

            $table->foreignId('pericia_id')
                ->constrained('pericias')
                ->onDelete('cascade');

            // Opcional — se quiser bônus por perícia no futuro
            $table->integer('bonus')->nullable();

            $table->timestamps();

            $table->unique(['origem_id', 'pericia_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('origem_pericia');
    }
};
