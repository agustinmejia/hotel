<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoomTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('room_types')->delete();
        
        \DB::table('room_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Simple',
                'description' => 'Habitación para una sola persona',
                'price' => '70.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:10:57',
                'updated_at' => '2023-10-11 22:10:57',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Doble',
                'description' => 'Habitación con 2 camas',
                'price' => '120.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:11:39',
                'updated_at' => '2023-10-11 22:11:39',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Triple',
                'description' => 'Habitación con 3 camas',
                'price' => '150.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:12:01',
                'updated_at' => '2023-10-11 22:12:01',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Cuadruple',
                'description' => 'Habitación con 4 camas',
                'price' => '200.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:12:26',
                'updated_at' => '2023-10-11 22:12:26',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Suit',
                'description' => NULL,
                'price' => '250.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:13:02',
                'updated_at' => '2023-10-11 22:13:02',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Matrimonial',
                'description' => NULL,
                'price' => '100.00',
                'status' => 1,
                'created_at' => '2023-10-11 22:13:59',
                'updated_at' => '2023-10-11 22:13:59',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}