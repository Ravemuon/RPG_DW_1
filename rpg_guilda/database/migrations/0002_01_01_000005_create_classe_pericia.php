<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classe_pericia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classe_id'); // FK para classes
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('cascade');
            $table->unsignedBigInteger('pericia_id'); // FK para perícias
            $table->foreign('pericia_id')->references('id')->on('pericias')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['classe_id','pericia_id']); // Impede duplicação de vínculos
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classe_pericia');
    }
};
