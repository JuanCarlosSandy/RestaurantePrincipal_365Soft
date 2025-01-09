<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
            $table->string('tipo_documento', 20)->nullable();
            $table->string('num_documento', 20)->nullable();
            $table->string('complemento_id', 20)->nullable();
            $table->string('direccion', 70)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->boolean('estadoCli')->default(true); // Agrega el campo estadoCli de tipo booleano con valor predeterminado true
            $table->timestamps();
        });

        DB::table('personas')->insert([
            ['id' => '1', 
            'nombre' => 'root default', 
            'tipo_documento' => '5', 
            'num_documento' => '00000000', 
            'direccion' => 'NA', 
            'telefono' => '000000', 
            'email' => 'root@gmail.com',
            'estadoCli' => false
            ],
            ['id' => '2', 
            'nombre' => 'vendedor default',
            'tipo_documento' => '5', 
            'num_documento' => '00000001', 
            'direccion' => 'NA', 
            'telefono' => '000000', 
            'email' => 'vend@gmail.com',
            'estadoCli' => false
            ],
            ['id' => '3', 
            'nombre' => 'almacenero default',
            'tipo_documento' => '5', 
            'num_documento' => '00000002', 
            'direccion' => 'NA', 
            'telefono' => '000000', 
            'email' => 'alma@gmail.com',
            'estadoCli' => false
            ],
            ['id' => '4', 
            'nombre' => 'Casos Especiales',
            'tipo_documento' => '5', 
            'num_documento' => '99001', 
            'direccion' => null, 
            'telefono' => null, 
            'email' => null,
            'estadoCli' => true
            ],
            ['id' => '5', 
            'nombre' => 'Control Tributario',
            'tipo_documento' => '5', 
            'num_documento' => '99002', 
            'direccion' => null, 
            'telefono' => null, 
            'email' => null,
            'estadoCli' => true
            ],
            ['id' => '6', 
            'nombre' => 'VENTAS MENORES DEL DÍA',
            'tipo_documento' => '5', 
            'num_documento' => '99003', 
            'direccion' => null, 
            'telefono' => null, 
            'email' => null,
            'estadoCli' => true
            ],
            ['id' => '7', 
            'nombre' => 'S/N',
            'tipo_documento' => '5', 
            'num_documento' => '0', 
            'direccion' => null, 
            'telefono' => null, 
            'email' => null,
            'estadoCli' => true
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
}