<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoItemArquivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido_item_arquivos', function (Blueprint $table) {
            $table->id();

			$table->unsignedBigInteger('pedido_item_id');
			$table->unsignedBigInteger('pedido_item_album_id')->nullable();
			$table->integer('folha')->nullable();
			$table->text('url_imagem')->nullable();
			$table->string('nome_arquivo');
			$table->integer('largura');
			$table->integer('altura');
			$table->integer('situacao')->default(0);

			$table->foreign('pedido_item_id')->references('id')->on('pedido_itens');
			$table->foreign('pedido_item_album_id')->references('id')->on('pedido_item_albuns');

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
        Schema::dropIfExists('pedido_item_arquivos');
    }
}
