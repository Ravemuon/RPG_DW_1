<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtributosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atributos', function (Blueprint $table) {
            $table->id(); // Cria um campo auto-incremental para o id
            $table->string('nome'); // Nome do atributo
            $table->unsignedBigInteger('sistema_id'); // Chave estrangeira para o sistema (relacionamento)
            $table->timestamps(); // Cria os campos created_at e updated_at
        });

        // Adicionando a chave estrangeira
        Schema::table('atributos', function (Blueprint $table) {
            $table->foreign('sistema_id')->references('id')->on('sistemas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('atributos');
    }
}
