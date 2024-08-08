<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerfilFuncaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfil_funcao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('perfil_id');
            $table->unsignedBiginteger('funcao_id');
            $table->timestamps();

            $table->foreign('perfil_id')->references('id')->on('perfis');
            $table->foreign('funcao_id')->references('id')->on('funcoes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perfil_funcao');
    }
}
