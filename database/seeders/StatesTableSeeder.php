<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('states')->delete();
        
        \DB::table('states')->insert(array (
            0 => 
            array (
                'id' => 1,
                'country_id' => 1,
                'name' => 'No definido',
                'created_at' => '2023-11-09 02:31:44',
                'updated_at' => '2023-11-09 02:31:44',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'country_id' => 2,
                'name' => 'Beni',
                'created_at' => '2023-11-09 02:31:50',
                'updated_at' => '2023-11-09 02:38:07',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'country_id' => 2,
                'name' => 'Santa Cruz',
                'created_at' => '2023-11-09 02:37:59',
                'updated_at' => '2023-11-09 02:37:59',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'country_id' => 2,
                'name' => 'La Paz',
                'created_at' => '2023-11-09 02:38:20',
                'updated_at' => '2023-11-09 02:38:20',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}