<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DataTypesTableSeeder::class,
            DataRowsTableSeeder::class,
            MenusTableSeeder::class,
            MenuItemsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            PermissionRoleTableSeeder::class,
            SettingsTableSeeder::class,
            UsersTableSeeder::class,
            CountriesTableSeeder::class,
            StatesTableSeeder::class,
            CitiesTableSeeder::class,
            BranchOfficesTableSeeder::class,
            RoomTypesTableSeeder::class,
            RoomAccessoriesTableSeeder::class,
            ProductTypesTableSeeder::class,
            ProductsTableSeeder::class,
            ServiceTypesTableSeeder::class,
            PeopleTableSeeder::class,
            RoomsTableSeeder::class,
            FoodTypesTableSeeder::class,
        ]);
    }
}
