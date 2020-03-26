<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsuransisTable extends Migration
{

    public function up()
    {
        Schema::create('asuransis', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nama');
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('asuransis');
    }
}
