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
            ['nombre' => '1'],
            ['nombre' => '2'],
            ['nombre' => '3'],
            ['nombre' => '4'],
            ['nombre' => '5'],
            ['nombre' => '6'],
            ['nombre' => '7'],
            ['nombre' => '8'],
            ['nombre' => '9'],
            ['nombre' => '10'],
            ['nombre' => '11'],
            ['nombre' => '12'],
            ['nombre' => '13'],
            ['nombre' => '14'],
            ['nombre' => '15'],
            ['nombre' => '16'],
            ['nombre' => '17'],
            ['nombre' => '18'],
            ['nombre' => '19'],
            ['nombre' => '20'],
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
