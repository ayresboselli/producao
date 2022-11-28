<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintOneProdutoItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_one_produto_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_produto');
            $table->integer('id_sax');
            $table->string('intervalo', 80)->nullable();
            $table->timestamps();

            $table->foreign('id_produto')->references('id')->on('print_one_produtos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('print_one_produto_items');
    }
}
