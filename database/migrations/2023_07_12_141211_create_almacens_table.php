<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlmacensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('almacens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_almacen', 100)->unique();
            $table->string('ubicacion')->nullable();
            $table->integer('encargado')->unsigned();
            $table->foreign('encargado')->references('id')->on('users');
            $table->integer('sucursal')->unsigned();
            $table->foreign('sucursal')->references('id')->on('sucursales');
            $table->string('telefono')->nullable();
            $table->string('observacion')->nullable();
            $table->boolean('condicion')->default(1);
            $table->timestamps();
        });


        DB::table('almacens')->insert(array('id' => '1',
            'nombre_almacen' => 'Almacen principal',
            'ubicacion' => 'Ubicación Predeterminada',
            'encargado' => '3',
            'sucursal' => '1',
            'telefono' => '78354613',
            'observacion' => 'Ninguna',
            ));
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('almacens');
    }
}
