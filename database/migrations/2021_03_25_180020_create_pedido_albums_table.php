<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido_albums', function (Blueprint $table) {
            $table->id();
			$table->integer('id_externo')->nullable();
			$table->unsignedBigInteger('id_pedido');
			$table->unsignedBigInteger('id_arquivo');
			$table->string('codigo',150);
            $table->timestamps();
			
			$table->foreign('id_pedido')->references('id')->on('pedidos');
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
