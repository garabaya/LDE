<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 27/09/2016
 * Time: 12:59
 */
use Illuminate\Database\Seeder;

class InitiativeTypesTableSeeder extends Seeder
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
        DB::table('initiativeTypes')->delete();
        DB::table('initiativeTypes')->insert([
            'id' => 1,
            'type' => 'general'
        ]);
        DB::table('initiativeTypes')->insert([
            'id' => 2,
            'type' => 'economy'
        ]);
    }
}