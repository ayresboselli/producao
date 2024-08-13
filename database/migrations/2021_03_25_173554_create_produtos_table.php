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

			$table->unsignedBigInteger('imposicao_tipo_id');
			$table->unsignedBigInteger('imposicao_nome_id')->nullable();
			$table->unsignedBigInteger('impressao_hotfolder_id');
			$table->unsignedBigInteger('impressao_substrato_id');

			$table->string('titulo',80);
			$table->integer('largura');
			$table->integer('altura');
			$table->boolean('sem_dimensao');
			$table->integer('sangr_sup')->nullable();
			$table->integer('sangr_inf')->nullable();
			$table->integer('sangr_esq')->nullable();
			$table->integer('sangr_dir')->nullable();
			$table->string('disposicao',80)->nullable();
			$table->boolean('renomear');
            $table->timestamps();

			$table->foreign('imposicao_tipo_id')->references('id')->on('imposicao_tipos');
			$table->foreign('imposicao_nome_id')->references('id')->on('imposicao_nomes');
			$table->foreign('impressao_hotfolder_id')->references('id')->on('impressao_hotfolders');
			$table->foreign('impressao_substrato_id')->references('id')->on('impressao_substratos');
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
