<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido_itens', function (Blueprint $table) {
            $table->id();
			$table->integer('id_externo')->nullable();
			$table->unsignedBigInteger('pedido_id');
			$table->unsignedBigInteger('produto_id')->nullable();
			$table->integer('id_produto_externo');
			$table->text('url_origem')->nullable();
            $table->timestamps();

			$table->foreign('pedido_id')->references('id')->on('pedidos');
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
        Schema::dropIfExists('pedido_items');
    }
}
