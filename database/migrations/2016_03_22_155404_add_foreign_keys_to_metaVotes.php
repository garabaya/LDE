<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToMetaVotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metaVotes', function (Blueprint $table) {
            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
                ->onDelete('cascade');
            $table->foreign('metaInitiative_id')
                ->references('id')
                ->on('metaInitiatives')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metaVotes', function (Blueprint $table) {
            $table->dropForeign('metaVotes_community_id_foreign');
            $table->dropForeign('metaVotes_metaInitiative_id_foreign');
        });
    }
}
