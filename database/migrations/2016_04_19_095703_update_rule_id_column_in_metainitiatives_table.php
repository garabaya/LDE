<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRuleIdColumnInMetainitiativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metaInitiatives', function (Blueprint $table) {
            $table->dropForeign('metainitiatives_rule_id_foreign');
            $table->dropColumn('rule_id');
            $table->unsignedInteger('community_rule_id')->index();
            $table->foreign('community_rule_id')
                ->references('id')
                ->on('community_rule')
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
            $table->dropForeign('metainitiatives_community_rule_id_foreign');
            $table->dropColumn('community_rule_id');
            $table->unsignedInteger('rule_id')->index();
            $table->foreign('rule_id')
                ->references('id')
                ->on('rules')
                ->onDelete('cascade');
        });
    }
}
