<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class JobsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('jobs')->delete();
        
        \DB::table('jobs')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Limpieza',
                'description' => NULL,
                'salary' => '3500.00',
                'created_at' => '2024-01-05 13:17:30',
                'updated_at' => '2024-01-05 13:17:30',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Recepcionista',
                'description' => NULL,
                'salary' => '4000.00',
                'created_at' => '2024-01-05 13:18:04',
                'updated_at' => '2024-01-05 13:18:04',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Guardia',
                'description' => NULL,
                'salary' => '3500.00',
                'created_at' => '2024-01-05 13:18:26',
                'updated_at' => '2024-01-05 13:18:26',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}