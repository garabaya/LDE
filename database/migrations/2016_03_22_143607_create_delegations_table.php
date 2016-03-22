<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelegationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delegations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('community_id')->index();
            $table->unsignedInteger('delegated_id')->index();
            $table->unsignedInteger('initiativeType_id')->index();
            $table->timestamps();
            $table->unique(array('community_id','delegated_id','initiativeType_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('delegations');
    }
}
