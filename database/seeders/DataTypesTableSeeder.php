<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DataTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('data_types')->delete();
        
        \DB::table('data_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'users',
                'slug' => 'users',
                'display_name_singular' => 'Usuario',
                'display_name_plural' => 'Usuarios',
                'icon' => 'voyager-person',
                'model_name' => 'TCG\\Voyager\\Models\\User',
                'policy_name' => 'TCG\\Voyager\\Policies\\UserPolicy',
                'controller' => 'TCG\\Voyager\\Http\\Controllers\\VoyagerUserController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"desc","default_search_key":null,"scope":null}',
                'created_at' => '2021-06-02 17:55:30',
                'updated_at' => '2023-10-10 23:34:25',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'menus',
                'slug' => 'menus',
                'display_name_singular' => 'Menu',
                'display_name_plural' => 'Menus',
                'icon' => 'voyager-list',
                'model_name' => 'TCG\\Voyager\\Models\\Menu',
                'policy_name' => NULL,
                'controller' => '',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => NULL,
                'created_at' => '2021-06-02 17:55:30',
                'updated_at' => '2021-06-02 17:55:30',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'roles',
                'slug' => 'roles',
                'display_name_singular' => 'Rol',
                'display_name_plural' => 'Roles',
                'icon' => 'voyager-lock',
                'model_name' => 'TCG\\Voyager\\Models\\Role',
                'policy_name' => NULL,
                'controller' => 'TCG\\Voyager\\Http\\Controllers\\VoyagerRoleController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"desc","default_search_key":null,"scope":null}',
                'created_at' => '2021-06-02 17:55:31',
                'updated_at' => '2023-10-10 23:45:27',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'permissions',
                'slug' => 'permissions',
                'display_name_singular' => 'Permiso',
                'display_name_plural' => 'Permisos',
                'icon' => 'voyager-list',
                'model_name' => 'App\\Models\\Permission',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":"table_name","order_display_column":"table_name","order_direction":"asc","default_search_key":null}',
                'created_at' => '2022-05-24 15:21:20',
                'updated_at' => '2022-05-24 15:21:20',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'room_types',
                'slug' => 'room-types',
                'display_name_singular' => 'Tipo de habitación',
                'display_name_plural' => 'Tipos de habitación',
                'icon' => 'voyager-tag',
                'model_name' => 'App\\Models\\RoomType',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2023-10-11 01:56:34',
                'updated_at' => '2023-10-11 02:07:45',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'room_accessories',
                'slug' => 'room-accessories',
                'display_name_singular' => 'Accesorio',
                'display_name_plural' => 'Accesorios',
                'icon' => 'voyager-puzzle',
                'model_name' => 'App\\Models\\RoomAccessory',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2023-10-11 02:11:18',
                'updated_at' => '2023-10-11 02:13:22',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'product_types',
                'slug' => 'product-types',
                'display_name_singular' => 'Tipo de producto',
                'display_name_plural' => 'Tipos de productos',
                'icon' => 'fa fa-tags',
                'model_name' => 'App\\Models\\ProductType',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null}',
                'created_at' => '2023-10-11 12:09:34',
                'updated_at' => '2023-10-11 12:09:34',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'service_types',
                'slug' => 'service-types',
                'display_name_singular' => 'Tipo de servicio',
                'display_name_plural' => 'Tipos de servicios',
                'icon' => 'voyager-window-list',
                'model_name' => 'App\\Models\\ServiceType',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null}',
                'created_at' => '2023-10-11 14:39:56',
                'updated_at' => '2023-10-11 14:39:56',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'branch_offices',
                'slug' => 'branch-offices',
                'display_name_singular' => 'Sucursal',
                'display_name_plural' => 'Sucursales',
                'icon' => 'fa fa-building',
                'model_name' => 'App\\Models\\BranchOffice',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null}',
                'created_at' => '2023-10-11 15:03:37',
                'updated_at' => '2023-10-11 15:03:37',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'people',
                'slug' => 'people',
                'display_name_singular' => 'Persona',
                'display_name_plural' => 'Personas',
                'icon' => 'voyager-people',
                'model_name' => 'App\\Models\\Person',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2023-10-11 15:27:50',
                'updated_at' => '2023-10-11 15:30:04',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'products',
                'slug' => 'products',
                'display_name_singular' => 'Producto',
                'display_name_plural' => 'Productos',
                'icon' => 'fa fa-shopping-basket',
                'model_name' => 'App\\Models\\Product',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2023-10-11 15:40:34',
                'updated_at' => '2023-10-11 15:42:13',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'rooms',
                'slug' => 'rooms',
                'display_name_singular' => 'Habitación',
                'display_name_plural' => 'Habitaciones',
                'icon' => 'fa fa-bed',
                'model_name' => 'App\\Models\\Room',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2023-10-11 16:11:42',
                'updated_at' => '2023-10-11 16:25:04',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'suppliers',
                'slug' => 'suppliers',
                'display_name_singular' => 'Proveedor',
                'display_name_plural' => 'Proveedores',
                'icon' => 'voyager-person',
                'model_name' => 'App\\Models\\Supplier',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2023-10-11 16:31:08',
                'updated_at' => '2023-10-11 16:32:20',
            ),
        ));
        
        
    }
}