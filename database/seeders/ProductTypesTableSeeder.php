<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        
        \DB::table('products')->delete();
        \DB::table('product_types')->delete();
        
        \DB::table('product_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Bebidas',
                'description' => NULL,
                'status' => 1,
                'created_at' => '2023-10-12 08:54:04',
                'updated_at' => '2023-10-12 08:54:04',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Golosinas',
                'description' => NULL,
                'status' => 1,
                'created_at' => '2023-10-12 08:54:30',
                'updated_at' => '2023-10-12 08:54:30',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Helados',
                'description' => NULL,
                'status' => 1,
                'created_at' => '2023-10-12 08:55:06',
                'updated_at' => '2023-10-12 08:55:06',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}