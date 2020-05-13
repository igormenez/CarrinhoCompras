<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCupomDescontosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupom_descontos', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->string('locaizador',70);
            $table->decimal('desconto',6,2);
            $table->enum('modo_desconto',['percent','valor']);
            $table->decimal('limite',6,2);
            $table->enum('modo_limite',['valor','qnt']);
            $table->dateTime('validade');
            $table->enum('ativo',['S','N']);
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
        Schema::dropIfExists('cupom_descontos');
    }
}
