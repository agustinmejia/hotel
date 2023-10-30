<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MenuItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('menu_items')->delete();
        
        \DB::table('menu_items')->insert(array (
            0 => 
            array (
                'id' => 1,
                'menu_id' => 1,
                'title' => 'Inicio',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'fa fa-dashboard',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 1,
                'created_at' => '2021-06-02 17:55:32',
                'updated_at' => '2023-10-19 00:46:46',
                'route' => 'voyager.dashboard',
                'parameters' => 'null',
            ),
            1 => 
            array (
                'id' => 2,
                'menu_id' => 1,
                'title' => 'Media',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-images',
                'color' => NULL,
                'parent_id' => 5,
                'order' => 3,
                'created_at' => '2021-06-02 17:55:32',
                'updated_at' => '2021-06-02 14:07:22',
                'route' => 'voyager.media.index',
                'parameters' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'menu_id' => 1,
                'title' => 'Usuarios',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-person',
                'color' => '#000000',
                'parent_id' => 11,
                'order' => 1,
                'created_at' => '2021-06-02 17:55:32',
                'updated_at' => '2022-05-24 15:06:46',
                'route' => 'voyager.users.index',
                'parameters' => 'null',
            ),
            3 => 
            array (
                'id' => 4,
                'menu_id' => 1,
                'title' => 'Roles',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-lock',
                'color' => NULL,
                'parent_id' => 11,
                'order' => 2,
                'created_at' => '2021-06-02 17:55:32',
                'updated_at' => '2021-06-02 14:08:05',
                'route' => 'voyager.roles.index',
                'parameters' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'menu_id' => 1,
                'title' => 'Herramientas',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-tools',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 7,
                'created_at' => '2021-06-02 17:55:32',
                'updated_at' => '2023-10-30 10:48:38',
                'route' => NULL,
                'parameters' => '',
            ),
            5 => 
            array (
                'id' => 6,
                'menu_id' => 1,
                'title' => 'Menu Builder',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-list',
                'color' => NULL,
                'parent_id' => 5,
                'order' => 1,
                'created_at' => '2021-06-02 17:55:33',
                'updated_at' => '2021-06-02 14:07:22',
                'route' => 'voyager.menus.index',
                'parameters' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'menu_id' => 1,
                'title' => 'Database',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-data',
                'color' => NULL,
                'parent_id' => 5,
                'order' => 2,
                'created_at' => '2021-06-02 17:55:33',
                'updated_at' => '2021-06-02 14:07:22',
                'route' => 'voyager.database.index',
                'parameters' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'menu_id' => 1,
                'title' => 'Compass',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-compass',
                'color' => NULL,
                'parent_id' => 5,
                'order' => 4,
                'created_at' => '2021-06-02 17:55:33',
                'updated_at' => '2021-06-02 14:07:22',
                'route' => 'voyager.compass.index',
                'parameters' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'menu_id' => 1,
                'title' => 'BREAD',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-bread',
                'color' => NULL,
                'parent_id' => 5,
                'order' => 5,
                'created_at' => '2021-06-02 17:55:33',
                'updated_at' => '2021-06-02 14:07:23',
                'route' => 'voyager.bread.index',
                'parameters' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'menu_id' => 1,
                'title' => 'Configuraciones',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-settings',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 6,
                'created_at' => '2021-06-02 17:55:33',
                'updated_at' => '2023-10-30 10:48:38',
                'route' => 'voyager.settings.index',
                'parameters' => 'null',
            ),
            10 => 
            array (
                'id' => 11,
                'menu_id' => 1,
                'title' => 'Seguridad',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-lock',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 5,
                'created_at' => '2021-06-02 14:07:53',
                'updated_at' => '2023-10-30 10:48:38',
                'route' => NULL,
                'parameters' => '',
            ),
            11 => 
            array (
                'id' => 12,
                'menu_id' => 1,
                'title' => 'Limpiar cache',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-refresh',
                'color' => '#000000',
                'parent_id' => 5,
                'order' => 6,
                'created_at' => '2021-06-25 18:03:59',
                'updated_at' => '2022-05-24 15:06:32',
                'route' => 'clear.cache',
                'parameters' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'menu_id' => 1,
                'title' => 'Permisos',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-list',
                'color' => NULL,
                'parent_id' => 11,
                'order' => 3,
                'created_at' => '2022-05-24 15:21:21',
                'updated_at' => '2022-05-24 15:21:31',
                'route' => 'voyager.permissions.index',
                'parameters' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'menu_id' => 1,
                'title' => 'Parámetros',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-params',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 4,
                'created_at' => '2023-10-11 01:54:11',
                'updated_at' => '2023-10-30 10:48:38',
                'route' => NULL,
                'parameters' => '',
            ),
            14 => 
            array (
                'id' => 15,
                'menu_id' => 1,
                'title' => 'Tipos de habitación',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-tag',
                'color' => NULL,
                'parent_id' => 14,
                'order' => 1,
                'created_at' => '2023-10-11 01:56:34',
                'updated_at' => '2023-10-11 01:56:46',
                'route' => 'voyager.room-types.index',
                'parameters' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'menu_id' => 1,
                'title' => 'Accesorios',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-puzzle',
                'color' => NULL,
                'parent_id' => 14,
                'order' => 2,
                'created_at' => '2023-10-11 02:11:18',
                'updated_at' => '2023-10-11 02:13:44',
                'route' => 'voyager.room-accessories.index',
                'parameters' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'menu_id' => 1,
                'title' => 'Tipos de productos',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'fa fa-tags',
                'color' => '#000000',
                'parent_id' => 14,
                'order' => 3,
                'created_at' => '2023-10-11 12:09:34',
                'updated_at' => '2023-10-11 12:16:22',
                'route' => 'voyager.product-types.index',
                'parameters' => 'null',
            ),
            17 => 
            array (
                'id' => 18,
                'menu_id' => 1,
                'title' => 'Tipos de servicios',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-window-list',
                'color' => NULL,
                'parent_id' => 14,
                'order' => 5,
                'created_at' => '2023-10-11 14:39:56',
                'updated_at' => '2023-10-11 15:40:46',
                'route' => 'voyager.service-types.index',
                'parameters' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'menu_id' => 1,
                'title' => 'Sucursales',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'fa fa-building',
                'color' => NULL,
                'parent_id' => 14,
                'order' => 6,
                'created_at' => '2023-10-11 15:03:37',
                'updated_at' => '2023-10-11 22:49:26',
                'route' => 'voyager.branch-offices.index',
                'parameters' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'menu_id' => 1,
                'title' => 'Personas',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-people',
                'color' => NULL,
                'parent_id' => 21,
                'order' => 1,
                'created_at' => '2023-10-11 15:27:50',
                'updated_at' => '2023-10-11 15:29:02',
                'route' => 'voyager.people.index',
                'parameters' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'menu_id' => 1,
                'title' => 'Administración',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-browser',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 3,
                'created_at' => '2023-10-11 15:28:58',
                'updated_at' => '2023-10-19 00:46:25',
                'route' => NULL,
                'parameters' => '',
            ),
            21 => 
            array (
                'id' => 22,
                'menu_id' => 1,
                'title' => 'Productos',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'fa fa-shopping-basket',
                'color' => NULL,
                'parent_id' => 14,
                'order' => 4,
                'created_at' => '2023-10-11 15:40:34',
                'updated_at' => '2023-10-11 15:40:46',
                'route' => 'voyager.products.index',
                'parameters' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'menu_id' => 1,
                'title' => 'Habitaciones',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'fa fa-bed',
                'color' => NULL,
                'parent_id' => 21,
                'order' => 2,
                'created_at' => '2023-10-11 16:11:42',
                'updated_at' => '2023-10-11 16:21:33',
                'route' => 'voyager.rooms.index',
                'parameters' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'menu_id' => 1,
                'title' => 'Proveedores',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-person',
                'color' => NULL,
                'parent_id' => 21,
                'order' => 3,
                'created_at' => '2023-10-11 16:31:08',
                'updated_at' => '2023-10-11 16:31:22',
                'route' => 'voyager.suppliers.index',
                'parameters' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'menu_id' => 1,
                'title' => 'Recepción',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'fa fa-calendar',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 2,
                'created_at' => '2023-10-19 00:46:16',
                'updated_at' => '2023-10-19 00:46:25',
                'route' => 'reservations.index',
                'parameters' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'menu_id' => 1,
                'title' => 'Stock de Productos',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'fa fa-cubes',
                'color' => '#000000',
                'parent_id' => 21,
                'order' => 4,
                'created_at' => '2023-10-30 10:48:24',
                'updated_at' => '2023-10-30 10:48:38',
                'route' => 'product-branch-offices.index',
                'parameters' => NULL,
            ),
        ));
        
        
    }
}