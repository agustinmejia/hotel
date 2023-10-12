<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('service_types')->delete();
        
        \DB::table('service_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Sauna',
                'description' => NULL,
                'price' => '20.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:43:20',
                'updated_at' => '2023-10-11 22:43:20',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Lavandería',
                'description' => NULL,
                'price' => '10.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:43:41',
                'updated_at' => '2023-10-11 22:43:41',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}