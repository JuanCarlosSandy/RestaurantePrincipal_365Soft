<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idsucursal')->unsigned();
            $table->integer('idusuario')->unsigned();
            $table->dateTime('fechaApertura');
            $table->dateTime('fechaCierre')->nullable();
            $table->decimal('saldoInicial', 11, 2);
            $table->decimal('depositos', 11, 2)->default('0.00');
            $table->decimal('salidas', 11, 2)->default('0.00');
            $table->decimal('tarifaqrdelivery', 11, 2)->default('0.00');
            $table->decimal('ventas', 11, 2)->default('0.00');
            $table->decimal('ventasContado', 11, 2)->default('0.00');
            $table->decimal('ventasQR', 11, 2)->default('0.00');
            $table->decimal('ventasTarjeta', 11, 2)->default('0.00');
            $table->decimal('compras', 11, 2)->default('0.00');
            $table->decimal('comprasContado', 11, 2)->default('0.00');
            $table->decimal('saldoFaltante', 11, 2)->default('0.00');
            $table->decimal('saldoSobrante', 11, 2)->default('0.00');
            $table->decimal('saldoCaja', 11, 2)->nullable();
            $table->decimal('saldototalventas', 11, 2)->nullable();;
            $table->boolean('estado')->default(1);
            $table->timestamps();

            $table->foreign('idsucursal')->references('id')->on('sucursales');
            $table->foreign('idusuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cajas');
    }
}
