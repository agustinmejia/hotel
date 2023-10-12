<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoomAccessoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('room_accessories')->delete();
        
        \DB::table('room_accessories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Aire acondicionado',
                'description' => NULL,
                'price' => '50.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:26:46',
                'updated_at' => '2023-10-11 22:26:46',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Ventilador de pie',
                'description' => NULL,
                'price' => '20.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:27:07',
                'updated_at' => '2023-10-11 22:27:07',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Frigobar',
                'description' => NULL,
                'price' => '30.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:27:28',
                'updated_at' => '2023-10-11 22:27:28',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}