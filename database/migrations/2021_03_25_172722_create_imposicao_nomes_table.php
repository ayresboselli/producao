<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImposicaoNomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imposicao_nomes', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('id_imposicao');
			$table->string('titulo',80);
			$table->text('descricao')->nullable();
            $table->timestamps();
			
			$table->foreign('id_imposicao')->references('id')->on('imposicao_tipos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imposicao_nomes');
    }
}
