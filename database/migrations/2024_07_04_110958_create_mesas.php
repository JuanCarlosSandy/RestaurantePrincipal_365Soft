<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMesas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mesas', function (Blueprint $table){
            $table->increments('id');
            $table->string('nombre', 250);
            $table->timestamps();
        });

        DB::table('mesas')->insert([
            ['nombre' => 'ONE'],
            ['nombre' => 'U2'],
            ['nombre' => 'THREE DOORS DOWN'],
            ['nombre' => 'FOUR'],
            ['nombre' => 'MANÁ'],
            ['nombre' => 'GAGA'],
            ['nombre' => 'XTINTOR'],
            ['nombre' => 'ACDC'],
            ['nombre' => 'ESPEJO'],
            ['nombre' => 'VAN GOGH'],
            ['nombre' => 'LED ZEPPELIN'],
            ['nombre' => 'OZZY OSBOURNE'],
            ['nombre' => 'ANCESTRAL'],
            ['nombre' => 'SILLON'],
            ['nombre' => 'RED BULL'],
            ['nombre' => 'HUARI'],
            ['nombre' => 'EXTRA'],
            ['nombre' => 'LUNA'],
            ['nombre' => 'BARRA 1'],
            ['nombre' => 'BARRA 2'],
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mesas');
    }
}
