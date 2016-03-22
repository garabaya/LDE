<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToVotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
                ->onDelete('cascade');
            $table->foreign('initiative_id')
                ->references('id')
                ->on('initiatives')
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
        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign('votes_community_id_foreign');
            $table->dropForeign('votes_initiative_id_foreign');
        });
    }
}
