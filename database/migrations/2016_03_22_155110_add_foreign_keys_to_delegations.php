<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToDelegations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delegations', function (Blueprint $table) {
            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
                ->onDelete('cascade');
            $table->foreign('delegated_id')
                ->references('id')
                ->on('communities')
                ->onDelete('cascade');
            $table->foreign('initiativeType_id')
                ->references('id')
                ->on('initiativeTypes')
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
        Schema::table('delegations', function (Blueprint $table) {
            $table->dropForeign('delegations_community_id_foreign');
            $table->dropForeign('delegations_delegated_id_foreign');
            $table->dropForeign('delegations_initiativeType_id_foreign');
        });
    }
}
