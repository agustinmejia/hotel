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
                'created_at' => '2021-06-02 13:55:32',
                'updated_at' => '2023-10-18 20:46:46',
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
                'created_at' => '2021-06-02 13:55:32',
                'updated_at' => '2023-10-31 10:14:11',
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
                'created_at' => '2021-06-02 13:55:32',
                'updated_at' => '2022-05-24 11:06:46',
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
                'created_at' => '2021-06-02 13:55:32',
                'updated_at' => '2021-06-02 10:08:05',
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
                'order' => 11,
                'created_at' => '2021-06-02 13:55:32',
                'updated_at' => '2024-03-01 00:56:34',
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
                'created_at' => '2021-06-02 13:55:33',
                'updated_at' => '2021-06-02 10:07:22',
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
                'created_at' => '2021-06-02 13:55:33',
                'updated_at' => '2023-10-31 10:14:11',
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
                'created_at' => '2021-06-02 13:55:33',
                'updated_at' => '2023-10-31 10:14:11',
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
                'created_at' => '2021-06-02 13:55:33',
                'updated_at' => '2023-10-31 10:14:11',
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
                'order' => 10,
                'created_at' => '2021-06-02 13:55:33',
                'updated_at' => '2024-03-01 00:56:34',
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
                'order' => 8,
                'created_at' => '2021-06-02 10:07:53',
                'updated_at' => '2023-11-18 13:04:49',
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
                'created_at' => '2021-06-25 14:03:59',
                'updated_at' => '2023-10-31 10:14:11',
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
                'created_at' => '2022-05-24 11:21:21',
                'updated_at' => '2022-05-24 11:21:31',
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
                'order' => 6,
                'created_at' => '2023-10-10 21:54:11',
                'updated_at' => '2023-11-18 13:04:49',
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
                'created_at' => '2023-10-10 21:56:34',
                'updated_at' => '2023-10-10 21:56:46',
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
                'created_at' => '2023-10-10 22:11:18',
                'updated_at' => '2023-10-10 22:13:44',
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
                'created_at' => '2023-10-11 08:09:34',
                'updated_at' => '2023-10-11 08:16:22',
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
                'created_at' => '2023-10-11 10:39:56',
                'updated_at' => '2023-10-11 11:40:46',
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
                'order' => 7,
                'created_at' => '2023-10-11 11:03:37',
                'updated_at' => '2023-12-11 21:45:25',
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
                'order' => 2,
                'created_at' => '2023-10-11 11:27:50',
                'updated_at' => '2023-11-06 04:43:58',
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
                'order' => 4,
                'created_at' => '2023-10-11 11:28:58',
                'updated_at' => '2023-11-18 13:04:49',
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
                'created_at' => '2023-10-11 11:40:34',
                'updated_at' => '2023-10-11 11:40:46',
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
                'order' => 3,
                'created_at' => '2023-10-11 12:11:42',
                'updated_at' => '2023-11-06 04:43:58',
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
                'order' => 4,
                'created_at' => '2023-10-11 12:31:08',
                'updated_at' => '2023-11-06 04:43:58',
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
                'created_at' => '2023-10-18 20:46:16',
                'updated_at' => '2023-11-06 04:37:33',
                'route' => 'reception.index',
                'parameters' => 'null',
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
                'order' => 5,
                'created_at' => '2023-10-30 06:48:24',
                'updated_at' => '2023-11-06 04:43:58',
                'route' => 'product-branch-offices.index',
                'parameters' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'menu_id' => 1,
                'title' => 'Cajas',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-logbook',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 5,
                'created_at' => '2023-10-31 10:13:56',
                'updated_at' => '2023-11-18 13:04:49',
                'route' => 'cashiers.index',
                'parameters' => 'null',
            ),
            27 => 
            array (
                'id' => 28,
                'menu_id' => 1,
                'title' => 'Reservaciones',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'fa fa-tag',
                'color' => '#000000',
                'parent_id' => 21,
                'order' => 1,
                'created_at' => '2023-11-06 04:43:50',
                'updated_at' => '2023-11-06 04:43:58',
                'route' => 'reservations.index',
                'parameters' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'menu_id' => 1,
                'title' => 'Paises',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-world',
                'color' => '#000000',
                'parent_id' => 14,
                'order' => 10,
                'created_at' => '2023-11-08 22:20:56',
                'updated_at' => '2023-12-19 18:23:36',
                'route' => 'voyager.countries.index',
                'parameters' => 'null',
            ),
            29 => 
            array (
                'id' => 30,
                'menu_id' => 1,
                'title' => 'Estados/Departamentos',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-puzzle',
                'color' => NULL,
                'parent_id' => 14,
                'order' => 11,
                'created_at' => '2023-11-08 22:23:01',
                'updated_at' => '2023-12-19 18:23:36',
                'route' => 'voyager.states.index',
                'parameters' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'menu_id' => 1,
                'title' => 'Ciudades',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-location',
                'color' => NULL,
                'parent_id' => 14,
                'order' => 12,
                'created_at' => '2023-11-08 22:24:58',
                'updated_at' => '2023-12-19 18:23:36',
                'route' => 'voyager.cities.index',
                'parameters' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'menu_id' => 1,
                'title' => 'Importar',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-upload',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 9,
                'created_at' => '2023-11-10 12:45:46',
                'updated_at' => '2024-03-01 00:56:34',
                'route' => 'import.index',
                'parameters' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'menu_id' => 1,
                'title' => 'Reportes',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-pie-graph',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 7,
                'created_at' => '2023-11-10 18:47:57',
                'updated_at' => '2023-11-18 13:04:49',
                'route' => NULL,
                'parameters' => '',
            ),
            33 => 
            array (
                'id' => 34,
                'menu_id' => 1,
                'title' => 'General',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-file-text',
                'color' => '#000000',
                'parent_id' => 33,
                'order' => 1,
                'created_at' => '2023-11-10 18:52:10',
                'updated_at' => '2023-11-10 18:52:23',
                'route' => 'report-general.index',
                'parameters' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'menu_id' => 1,
                'title' => 'Vender',
                'url' => 'admin/sell',
                'target' => '_self',
                'icon_class' => 'fa fa-shopping-cart',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 3,
                'created_at' => '2023-11-18 13:04:14',
                'updated_at' => '2023-11-18 13:51:40',
                'route' => NULL,
                'parameters' => '',
            ),
            35 => 
            array (
                'id' => 36,
                'menu_id' => 1,
                'title' => 'Tipo de multas',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-warning',
                'color' => NULL,
                'parent_id' => 14,
                'order' => 8,
                'created_at' => '2023-11-19 18:58:32',
                'updated_at' => '2023-12-11 21:45:25',
                'route' => 'voyager.penalty-types.index',
                'parameters' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'menu_id' => 1,
                'title' => 'Cargos',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-list',
                'color' => NULL,
                'parent_id' => 14,
                'order' => 6,
                'created_at' => '2023-12-11 21:44:53',
                'updated_at' => '2023-12-11 21:45:25',
                'route' => 'voyager.jobs.index',
                'parameters' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'menu_id' => 1,
                'title' => 'Empleados',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-person',
                'color' => NULL,
                'parent_id' => 21,
                'order' => 6,
                'created_at' => '2023-12-11 21:56:12',
                'updated_at' => '2023-12-11 21:56:26',
                'route' => 'voyager.employes.index',
                'parameters' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'menu_id' => 1,
                'title' => 'Refrigerios',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-bread',
                'color' => NULL,
                'parent_id' => 14,
                'order' => 9,
                'created_at' => '2023-12-19 18:23:19',
                'updated_at' => '2023-12-19 18:23:36',
                'route' => 'voyager.food-types.index',
                'parameters' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'menu_id' => 1,
                'title' => 'Servicios',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-tag',
                'color' => '#000000',
                'parent_id' => 33,
                'order' => 3,
                'created_at' => '2023-12-19 19:55:01',
                'updated_at' => '2023-12-19 19:57:09',
                'route' => 'report-services.index',
                'parameters' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'menu_id' => 1,
                'title' => 'Adelantos',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-dollar',
                'color' => '#000000',
                'parent_id' => 33,
                'order' => 2,
                'created_at' => '2023-12-19 19:57:03',
                'updated_at' => '2023-12-19 19:57:09',
                'route' => 'report-employes-payments.index',
                'parameters' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
                'menu_id' => 1,
                'title' => 'Limpieza',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-brush',
                'color' => '#000000',
                'parent_id' => 33,
                'order' => 4,
                'created_at' => '2023-12-29 06:41:37',
                'updated_at' => '2023-12-29 06:41:42',
                'route' => 'report-employes-cleaning.index',
                'parameters' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
                'menu_id' => 1,
                'title' => 'Planilla de Pagos',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-file-text',
                'color' => '#000000',
                'parent_id' => 33,
                'order' => 5,
                'created_at' => '2023-12-29 11:30:39',
                'updated_at' => '2023-12-29 11:32:04',
                'route' => 'report-employes-debts.index',
                'parameters' => 'null',
            ),
            43 => 
            array (
                'id' => 44,
                'menu_id' => 1,
                'title' => 'Registros de caja',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-logbook',
                'color' => '#000000',
                'parent_id' => 33,
                'order' => 6,
                'created_at' => '2024-03-01 00:56:18',
                'updated_at' => '2024-03-01 00:56:34',
                'route' => 'report-cashiers.registers.index',
                'parameters' => NULL,
            ),
        ));
        
        
    }
}