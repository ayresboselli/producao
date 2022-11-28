<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReimpressaoAlbumPedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimpressao_album_pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo',80);
            $table->integer('produto');
            $table->integer('ordem_producao');
            $table->timestamp('processada')->nullable();
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
        Schema::dropIfExists('reimpressao_album_pedidos');
    }
}
