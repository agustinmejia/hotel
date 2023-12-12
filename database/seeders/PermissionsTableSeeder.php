<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $keys = [
            'browse_admin',
            'browse_bread',
            'browse_database',
            'browse_media',
            'browse_compass',
        ];

        foreach ($keys as $key) {
            Permission::firstOrCreate([
                'key'        => $key,
                'table_name' => null,
            ]);
        }

        Permission::generateFor('menus');
        Permission::generateFor('roles');
        Permission::generateFor('users');
        Permission::generateFor('settings');
        Permission::generateFor('permissions');
        Permission::generateFor('room_types');
        Permission::generateFor('room_accessories');
        Permission::generateFor('product_types');
        Permission::generateFor('products');
        Permission::generateFor('service_types');
        Permission::generateFor('branch_offices');
        Permission::generateFor('people');
        Permission::generateFor('rooms');
        Permission::generateFor('suppliers');
        Permission::generateFor('reservations');
        Permission::generateFor('product_branch_offices');
        Permission::generateFor('cashiers');
        Permission::generateFor('countries');
        Permission::generateFor('states');
        Permission::generateFor('cities');
        Permission::generateFor('sales');
        Permission::generateFor('penalty_types');
        Permission::generateFor('jobs');
        Permission::generateFor('employes');

        // Sale
        Permission::firstOrCreate([
            'key'        => 'browse_sell',
            'table_name' => 'sales',
        ]);

        // Import
        Permission::firstOrCreate([
            'key'        => 'browse_import',
            'table_name' => 'import',
        ]);

        // Report
        Permission::firstOrCreate([
            'key'        => 'browse_report-general',
            'table_name' => 'reports',
        ]);
    }
}
