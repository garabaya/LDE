<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetaVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metaVotes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('community_id')->index();
            $table->unsignedInteger('metaInitiative_id')->index();
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('metaVotes');
    }
}
