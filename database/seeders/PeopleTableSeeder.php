<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PeopleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('people')->delete();
        
        \DB::table('people')->insert(array (
            0 => 
            array (
                'id' => 1,
                'full_name' => 'Sin nombre',
                'dni' => '000000',
                'phone' => '',
                'address' => NULL,
                'birthday' => '1991-04-20',
                'origin' => NULL,
                'job' => NULL,
                'photo' => NULL,
                'created_at' => '2023-11-04 13:29:13',
                'updated_at' => '2023-11-04 13:29:13',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}