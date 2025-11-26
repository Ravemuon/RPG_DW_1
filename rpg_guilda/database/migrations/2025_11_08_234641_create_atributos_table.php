<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtributosTable extends Migration
{
    public function up()
    {
        Schema::create('atributos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->unsignedBigInteger('sistema_id');
            $table->timestamps();
        });

        Schema::table('atributos', function (Blueprint $table) {
            $table->foreign('sistema_id')
                ->references('id')
                ->on('sistemas')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('atributos');
    }
}
