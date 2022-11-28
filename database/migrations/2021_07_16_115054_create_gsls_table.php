<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGslsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gsls', function (Blueprint $table) {
            $table->id();
            
            $table->string('nome_album', 150);
            $table->integer('ordem_servico');
            $table->integer('ordem_producao');
            $table->integer('tipo_pedido');
            $table->string('path');
            $table->string('album', 50);
            $table->integer('quantidade');
            $table->boolean('correcao');
            $table->timestamp('dt_correcao_entrada')->nullable();
            $table->timestamp('dt_correcao_saida')->nullable();
            $table->timestamp('dt_imposicao_entrada')->nullable();
            $table->timestamp('dt_imposicao_saida')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gsls');
    }
}
