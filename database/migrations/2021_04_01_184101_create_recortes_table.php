<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecortesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recortes', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('pedido_item_arquivo_id');
			$table->unsignedBigInteger('user_id')->nullable();
			$table->integer('crop_pos_x')->nullable();
			$table->integer('crop_pos_y')->nullable();
			$table->integer('crop_largura')->nullable();
			$table->integer('crop_altura')->nullable();
			$table->integer('situacao')->default(0);
            $table->timestamps();

			$table->foreign('pedido_item_arquivo_id')->references('id')->on('pedido_item_arquivos');
			$table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recortes');
    }
}
