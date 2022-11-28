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
			$table->unsignedBigInteger('id_arquivo');
			$table->unsignedBigInteger('id_usuario')->nullable();
			$table->integer('crop_pos_x')->nullable();
			$table->integer('crop_pos_y')->nullable();
			$table->integer('crop_largura')->nullable();
			$table->integer('crop_altura')->nullable();
			$table->integer('situacao')->default(0);
            $table->timestamps();
			
			$table->foreign('id_arquivo')->references('id')->on('pedido_item_arquivos');
			$table->foreign('id_usuario')->references('id')->on('users');
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
