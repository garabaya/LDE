<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCommunityRuleTable
 *
 * Converting the relationship between rule and community from a 'One to Many' into a 'Many to many' ralationship
 * This is the pivot table
 */
class CreateCommunityRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_rule', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('community_id')->index();
            $table->unsignedInteger('rule_id')->index();
            $table->timestamps();
            $table->unique(array('community_id', 'rule_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('community_rule', function (Blueprint $table) {
            Schema::drop('community_rule');

        });
    }
}
