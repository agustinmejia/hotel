<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('countries')->delete();
        
        \DB::table('countries')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'No definido',
                'created_at' => '2023-11-09 02:27:57',
                'updated_at' => '2023-11-09 02:27:57',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Bolivia',
                'created_at' => '2023-11-09 02:28:03',
                'updated_at' => '2023-11-09 02:28:03',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Perú',
                'created_at' => '2023-11-09 02:28:10',
                'updated_at' => '2023-11-09 02:28:10',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Argentina',
                'created_at' => '2023-11-09 02:28:16',
                'updated_at' => '2023-11-09 02:28:16',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Chile',
                'created_at' => '2023-11-09 02:28:21',
                'updated_at' => '2023-11-09 02:28:21',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Venezuela',
                'created_at' => '2023-11-09 02:28:32',
                'updated_at' => '2023-11-09 02:28:32',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Colombia',
                'created_at' => '2023-11-09 02:28:37',
                'updated_at' => '2023-11-09 02:28:37',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'México',
                'created_at' => '2023-11-09 02:28:46',
                'updated_at' => '2023-11-09 02:28:46',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Brasil',
                'created_at' => '2023-11-09 02:28:57',
                'updated_at' => '2023-11-09 02:28:57',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}