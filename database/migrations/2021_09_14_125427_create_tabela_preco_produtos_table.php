<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabelaPrecoProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabela_preco_produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tabela_preco_id');
            $table->unsignedBigInteger('produto_id');
            $table->float('preco');
            $table->timestamps();

            $table->foreign('tabela_preco_id')->references('id')->on('tabela_precos');
            $table->foreign('produto_id')->references('id')->on('produtos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tabela_preco_produtos');
    }
}
