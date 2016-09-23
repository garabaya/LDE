<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupportedApprovedColumnsToMetainitiativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metaInitiatives', function (Blueprint $table) {
            $table->boolean('supported')->nullable();
            $table->boolean('approved')->nullable();
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
            $table->dropColumn('supported');
            $table->dropColumn('approved');
        });
    }
}
