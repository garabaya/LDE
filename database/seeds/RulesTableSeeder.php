<?php

use Illuminate\Database\Seeder;

class RulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Rules will not change and we will need a fixed id for each one
         */
        DB::table('rules')->delete();
        DB::table('rules')->insert([
            'id' => 1,
            'type' => 'boolean',
            'description' => 'public community'
        ]);
        DB::table('rules')->insert([
            'id' => 2,
            'type' => 'numeric',
            'description' => 'available days to support an initiative'
        ]);
        DB::table('rules')->insert([
            'id' => 3,
            'type' => 'numeric',
            'description' => 'percentage of supporters required for an initiative to be voted'
        ]);
    }
}
