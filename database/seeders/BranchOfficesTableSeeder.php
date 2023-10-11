<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BranchOfficesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('branch_offices')->delete();
        
        \DB::table('branch_offices')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Casa matriz',
                'address' => NULL,
                'phone' => NULL,
                'location' => NULL,
                'images' => NULL,
                'status' => 1,
                'created_at' => '2023-10-11 15:06:24',
                'updated_at' => '2023-10-11 15:06:24',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}