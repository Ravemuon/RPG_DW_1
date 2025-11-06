<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampanhaUsuarioPivotTable extends Migration // <- nome único
{
    public function up()
    {
        Schema::create('campanha_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campanha_id')->constrained('campanhas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('ativo'); // opcional
            $table->timestamps();

            $table->unique(['campanha_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('campanha_usuario');
    }
}
