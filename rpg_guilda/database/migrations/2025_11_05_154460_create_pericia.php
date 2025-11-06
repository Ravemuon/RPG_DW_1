<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pericias', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('sistemaRPG', 50);
            $table->boolean('automatica')->default(false);
            $table->json('formula')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pericias');
    }
};
