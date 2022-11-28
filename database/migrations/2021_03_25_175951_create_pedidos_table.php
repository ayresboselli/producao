<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
			$table->integer('id_externo');
			$table->boolean('tipo_contrato')->default(false);
			$table->integer('id_cliente');
			$table->string('cliente',150);
			$table->string('contrato', 80)->nullable();
			$table->date('data_entrada');
			$table->date('previsao_entrega');
			$table->boolean('deletar_origem')->default(false);
            $table->timestamps();
			
			$table->unique(['id_externo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
}
