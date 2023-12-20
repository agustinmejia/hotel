<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FoodTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('food_types')->delete();
        
        \DB::table('food_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Desayuno',
                'description' => NULL,
                'status' => 1,
                'created_at' => '2023-12-19 22:23:51',
                'updated_at' => '2023-12-19 22:23:51',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Almuerzo',
                'description' => NULL,
                'status' => 0,
                'created_at' => '2023-12-19 22:23:58',
                'updated_at' => '2023-12-19 22:24:26',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Medienta - tarde',
                'description' => NULL,
                'status' => 0,
                'created_at' => '2023-12-19 22:24:07',
                'updated_at' => '2023-12-19 22:24:21',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Cena',
                'description' => NULL,
                'status' => 0,
                'created_at' => '2023-12-19 22:24:13',
                'updated_at' => '2023-12-19 22:24:18',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}