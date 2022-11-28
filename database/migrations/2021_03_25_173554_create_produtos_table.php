<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
			$table->integer('id_externo')->nullable();
			
			$table->unsignedBigInteger('id_imposicao_tipo');
			$table->unsignedBigInteger('id_imposicao_nome')->nullable();
			$table->unsignedBigInteger('id_impressao_hotfolder');
			$table->unsignedBigInteger('id_impressao_substrato');
			
			$table->string('titulo',80);
			$table->integer('largura');
			$table->integer('altura');
			$table->integer('sangr_sup')->nullable();
			$table->integer('sangr_inf')->nullable();
			$table->integer('sangr_esq')->nullable();
			$table->integer('sangr_dir')->nullable();
			$table->string('disposicao',80)->nullable();
            $table->timestamps();
			
			$table->foreign('id_imposicao_tipo')->references('id')->on('imposicao_tipos');
			$table->foreign('id_imposicao_nome')->references('id')->on('imposicao_nomes');
			$table->foreign('id_impressao_hotfolder')->references('id')->on('impressao_hotfolders');
			$table->foreign('id_impressao_substrato')->references('id')->on('impressao_substratos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produtos');
    }
}
