<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReimpressaoAlbumLaminasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimpressao_album_laminas', function (Blueprint $table) {
            $table->id();
            $table->integer('foto_frente');
            $table->integer('foto_verso');
            $table->string('album', 80);
            $table->string('defeito_celula', 80);
            $table->string('defeito_descricao', 150);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('reimpressao_album_laminas');
    }
}
