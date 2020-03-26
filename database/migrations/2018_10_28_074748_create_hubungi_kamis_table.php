<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHubungiKamisTable extends Migration
{

    public function up()
    {
        Schema::create('hubungi_kamis', function(Blueprint $table) {
            $table->increments('id');
            $table->('emai');
            $table->string('pesan');
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('hubungi_kamis');
    }
}
