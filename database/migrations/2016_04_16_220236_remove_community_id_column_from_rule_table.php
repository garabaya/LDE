<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class RemoveCommunityIdColumnFromRuleTable
 *
 *
 * Converting the relationship between rule and community from a 'One to Many' into a 'Many to many' ralationship
 *  so community_id column now is not necessary.
 * We will use now a pivot table called community_rule
 */
class RemoveCommunityIdColumnFromRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->dropForeign('rules_community_id_foreign');
        });
        Schema::table('rules',function($table){
           $table->dropColumn('community_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rules',function($table){
            $table->unsignedInteger('community_id')->index();
        });
        Schema::table('rules', function (Blueprint $table) {
            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
                ->onDelete('cascade');
        });
    }
}
