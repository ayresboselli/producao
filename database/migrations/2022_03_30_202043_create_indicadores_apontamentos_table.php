<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndicadoresApontamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicadores_apontamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lamina')->nullable()
            $table->unsignedBigInteger('id_indicador');
            $table->unsignedBigInteger('id_celula_ident');
            $table->unsignedBigInteger('id_celula_falha');
            
            $table->timestamps();
            
            $table->foreign('id_lamina')->references('id')->on('reimpressao_album_laminas');
            $table->foreign('id_indicador')->references('id')->on('indicadores_listas');
            $table->foreign('id_celula_ident')->references('id')->on('celulas');
            $table->foreign('id_celula_falha')->references('id')->on('celulas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('indicadores_apontamentos');
    }
}
