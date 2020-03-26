<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiagnosasTable extends Migration
{

    public function up()
    {
        Schema::create('diagnosas', function(Blueprint $table) {
            $table->increments('id');
            $table->Diagnosa('Kode');
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('diagnosas');
    }
}
