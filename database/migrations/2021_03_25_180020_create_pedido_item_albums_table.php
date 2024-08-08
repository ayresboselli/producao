<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoItemAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido_item_albuns', function (Blueprint $table) {
            $table->id();
			$table->integer('id_externo')->nullable();
			$table->unsignedBigInteger('pedido_item_id');
			$table->string('codigo',150);
            $table->timestamps();

			$table->foreign('pedido_item_id')->references('id')->on('pedido_itens');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido_albums');
    }
}
