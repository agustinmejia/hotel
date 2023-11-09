<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cities')->delete();
        
        \DB::table('cities')->insert(array (
            0 => 
            array (
                'id' => 1,
                'state_id' => 1,
                'name' => 'No definida',
                'province' => NULL,
                'created_at' => '2023-11-09 02:33:43',
                'updated_at' => '2023-11-09 02:33:43',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'state_id' => 2,
                'name' => 'Santísima Trinidad',
                'province' => 'cercado',
                'created_at' => '2023-11-09 02:38:44',
                'updated_at' => '2023-11-09 02:45:11',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'state_id' => 2,
                'name' => 'Guayaramerín',
                'province' => 'vaca diez',
                'created_at' => '2023-11-09 02:38:58',
                'updated_at' => '2023-11-09 02:45:05',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'state_id' => 2,
                'name' => 'Riberalta',
                'province' => 'vaca diez',
                'created_at' => '2023-11-09 02:39:06',
                'updated_at' => '2023-11-09 02:45:01',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}