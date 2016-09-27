<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 27/09/2016
 * Time: 12:59
 */
use Illuminate\Database\Seeder;

class InitiativetypesTableSeeder extends Seeder
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
        DB::table('initiativetypes')->delete();
        DB::table('initiativetypes')->insert([
            'id' => 1,
            'type' => 'general'
        ]);
        DB::table('initiativetypes')->insert([
            'id' => 2,
            'type' => 'economy'
        ]);
    }
}