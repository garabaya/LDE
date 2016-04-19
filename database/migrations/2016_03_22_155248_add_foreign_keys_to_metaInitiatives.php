<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToMetaInitiatives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metaInitiatives', function (Blueprint $table) {
            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
                ->onDelete('cascade');
            $table->foreign('rule_id')
                ->references('id')
                ->on('rules')
                ->onDelete('cascade');
            $table->foreign('thread_id')
                ->references('id')
                ->on('threads')
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
        Schema::table('metaInitiatives', function (Blueprint $table) {
            $table->dropForeign('metainitiatives_community_id_foreign');
            $table->dropForeign('metainitiatives_rule_id_foreign');
            $table->dropForeign('metainitiatives_thread_id_foreign');
        });
    }
}
