<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoomsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('rooms')->delete();
        
        \DB::table('rooms')->insert(array (
            0 => 
            array (
                'id' => 1,
                'room_type_id' => 1,
                'floor_number' => 1,
                'code' => '1-A',
                'details' => NULL,
                'images' => NULL,
                'status' => 'disponible',
                'created_at' => '2023-10-11 22:51:38',
                'updated_at' => '2023-10-11 22:51:38',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'room_type_id' => 2,
                'floor_number' => 1,
                'code' => '1-B',
                'details' => NULL,
                'images' => NULL,
                'status' => 'disponible',
                'created_at' => '2023-10-11 22:51:50',
                'updated_at' => '2023-10-11 22:51:50',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'room_type_id' => 2,
                'floor_number' => 1,
                'code' => '1-C',
                'details' => NULL,
                'images' => NULL,
                'status' => 'disponible',
                'created_at' => '2023-10-11 22:52:11',
                'updated_at' => '2023-10-11 22:52:11',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'room_type_id' => 1,
                'floor_number' => 2,
                'code' => '2-A',
                'details' => NULL,
                'images' => NULL,
                'status' => 'disponible',
                'created_at' => '2023-10-11 22:52:25',
                'updated_at' => '2023-10-11 22:52:25',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'room_type_id' => 5,
                'floor_number' => 3,
                'code' => '3-A',
                'details' => NULL,
                'images' => NULL,
                'status' => 'disponible',
                'created_at' => '2023-10-11 22:52:56',
                'updated_at' => '2023-10-11 22:52:56',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}