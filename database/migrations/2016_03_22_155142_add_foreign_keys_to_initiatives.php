<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToInitiatives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('initiatives', function (Blueprint $table) {
            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
                ->onDelete('cascade');
            $table->foreign('scoped_id')
                ->references('id')
                ->on('communities')
                ->onDelete('cascade');
            $table->foreign('initiativeType_id')
                ->references('id')
                ->on('initiativeTypes')
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
        Schema::table('initiatives', function (Blueprint $table) {
            $table->dropForeign('initiatives_community_id_foreign');
            $table->dropForeign('initiatives_scoped_id_foreign');
            $table->dropForeign('initiatives_initiativeType_id_foreign');
            $table->dropForeign('initiatives_thread_id_foreign');
        });
    }
}
