<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('id_produto');
			$table->string('titulo',80);
			$table->enum('tipo_upload', ['A', 'P'])->comment('Arquivo / Pasta')->default('P');
			$table->enum('replicar', ['S','N','?'])->default('N')->comment('Sim / Não / ? define no momento do upload');
            $table->timestamps();
			
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
        Schema::dropIfExists('uploads');
    }
}
