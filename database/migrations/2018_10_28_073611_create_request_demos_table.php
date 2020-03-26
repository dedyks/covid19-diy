<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestDemosTable extends Migration
{

    public function up()
    {
        Schema::create('request_demos', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('project_id')->unsigned();
            $table->date('due');
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('request_demos');
    }
}
