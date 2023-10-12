<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('products')->delete();
        
        \DB::table('products')->insert(array (
            0 => 
            array (
                'id' => 1,
                'product_type_id' => 1,
                'name' => 'Coca cola 2 litros',
                'description' => NULL,
                'price' => '12.00',
                'images' => NULL,
                'status' => 1,
                'created_at' => '2023-10-11 22:39:48',
                'updated_at' => '2023-10-11 22:40:42',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'product_type_id' => 1,
                'name' => 'Agua Purita 2 litros',
                'description' => NULL,
                'price' => '12.00',
                'images' => NULL,
                'status' => 1,
                'created_at' => '2023-10-11 22:40:19',
                'updated_at' => '2023-10-11 22:40:19',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'product_type_id' => 1,
                'name' => 'Fanta 2 litros',
                'description' => NULL,
                'price' => '12.00',
                'images' => NULL,
                'status' => 1,
                'created_at' => '2023-10-11 22:40:35',
                'updated_at' => '2023-10-11 22:40:35',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'product_type_id' => 2,
                'name' => 'Galleta oreo',
                'description' => NULL,
                'price' => '2.50',
                'images' => NULL,
                'status' => 1,
                'created_at' => '2023-10-11 22:41:02',
                'updated_at' => '2023-10-11 22:41:02',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'product_type_id' => 2,
                'name' => 'Galleta Club Social',
                'description' => NULL,
                'price' => '3.00',
                'images' => NULL,
                'status' => 1,
                'created_at' => '2023-10-11 22:41:19',
                'updated_at' => '2023-10-11 22:41:19',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}