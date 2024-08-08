<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoItemServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido_item_servicos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_externo')->nullable();
			$table->unsignedBigInteger('pedido_id');
			$table->unsignedBigInteger('servico_id')->nullable();
			$table->integer('id_servico_externo');
			$table->text('url_origem')->nullable();
            $table->timestamps();

			$table->foreign('pedido_id')->references('id')->on('pedidos');
			$table->foreign('servico_id')->references('id')->on('servicos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido_item_servicos');
    }
}
