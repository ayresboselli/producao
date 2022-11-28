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
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
			$table->integer('id_externo')->nullable();
			$table->unsignedBigInteger('id_pedido');
			$table->unsignedBigInteger('id_produto')->nullable();
			$table->integer('id_produto_externo');
			$table->text('url_origem')->nullable();
            $table->timestamps();
			
			$table->foreign('id_pedido')->references('id')->on('pedidos');
			$table->foreign('id_produto')->references('id')->on('produtos');
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
