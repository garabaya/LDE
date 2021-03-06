<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetaInitiativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metaInitiatives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->mediumText('description');
            $table->unsignedInteger('community_id')->index();
            $table->unsignedInteger('rule_id')->index();
            $table->unsignedInteger('thread_id')->index();
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
        Schema::drop('metaInitiatives');
    }
}
