<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'site.title',
                'display_name' => 'Site Title',
                'value' => 'Template',
                'details' => '',
                'type' => 'text',
                'order' => 1,
                'group' => 'Site',
            ),
            1 => 
            array (
                'id' => 2,
                'key' => 'site.description',
                'display_name' => 'Site Description',
                'value' => 'Template Laravel y Voyager',
                'details' => '',
                'type' => 'text',
                'order' => 2,
                'group' => 'Site',
            ),
            2 => 
            array (
                'id' => 3,
                'key' => 'site.logo',
                'display_name' => 'Site Logo',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 3,
                'group' => 'Site',
            ),
            3 => 
            array (
                'id' => 4,
                'key' => 'site.google_analytics_tracking_id',
                'display_name' => 'Google Analytics Tracking ID',
                'value' => NULL,
                'details' => '',
                'type' => 'text',
                'order' => 4,
                'group' => 'Site',
            ),
            4 => 
            array (
                'id' => 5,
                'key' => 'admin.bg_image',
                'display_name' => 'Admin Background Image',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 4,
                'group' => 'Admin',
            ),
            5 => 
            array (
                'id' => 6,
                'key' => 'admin.title',
                'display_name' => 'Admin Title',
                'value' => 'Hotel Admin',
                'details' => '',
                'type' => 'text',
                'order' => 1,
                'group' => 'Admin',
            ),
            6 => 
            array (
                'id' => 7,
                'key' => 'admin.description',
                'display_name' => 'Admin Description',
                'value' => 'Sistema Web de Administración de Hoteles',
                'details' => '',
                'type' => 'text',
                'order' => 1,
                'group' => 'Admin',
            ),
            7 => 
            array (
                'id' => 8,
                'key' => 'admin.loader',
                'display_name' => 'Admin Loader',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 2,
                'group' => 'Admin',
            ),
            8 => 
            array (
                'id' => 9,
                'key' => 'admin.icon_image',
                'display_name' => 'Admin Icon Image',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 3,
                'group' => 'Admin',
            ),
            9 => 
            array (
                'id' => 10,
                'key' => 'admin.google_analytics_client_id',
            'display_name' => 'Google Analytics Client ID (used for admin dashboard)',
                'value' => NULL,
                'details' => '',
                'type' => 'text',
                'order' => 5,
                'group' => 'Admin',
            ),
            10 => 
            array (
                'id' => 11,
                'key' => 'system.enabled',
                'display_name' => 'Enabled',
                'value' => '1',
                'details' => NULL,
                'type' => 'checkbox',
                'order' => 12,
                'group' => 'System',
            ),
            11 => 
            array (
                'id' => 12,
                'key' => 'system.whatsapp-server',
                'display_name' => 'Servidor de Whatsapp',
                'value' => 'https://wa1.desarrollocreativo.dev',
                'details' => NULL,
                'type' => 'text',
                'order' => 6,
                'group' => 'System',
            ),
            12 => 
            array (
                'id' => 13,
                'key' => 'system.whatsapp-session',
                'display_name' => 'Sesión de Whatsapp',
                'value' => 'hotel-tarope',
                'details' => NULL,
                'type' => 'text',
                'order' => 7,
                'group' => 'System',
            ),
            13 => 
            array (
                'id' => 14,
                'key' => 'system.update_hosting',
                'display_name' => 'Actualización de hospedajes',
                'value' => '11:00',
                'details' => NULL,
                'type' => 'text',
                'order' => 9,
                'group' => 'System',
            ),
            14 => 
            array (
                'id' => 15,
                'key' => 'system.phone-admin',
                'display_name' => 'Celular del administrador',
                'value' => '69373572',
                'details' => NULL,
                'type' => 'text',
                'order' => 8,
                'group' => 'System',
            ),
            15 => 
            array (
                'id' => 16,
                'key' => 'system.required_guest',
                'display_name' => 'Nombre de huesped requerido',
                'value' => '0',
                'details' => NULL,
                'type' => 'checkbox',
                'order' => 10,
                'group' => 'System',
            ),
            16 => 
            array (
                'id' => 17,
                'key' => 'system.group_by',
                'display_name' => 'Dashboard agrupado por',
                'value' => 'type.name',
                'details' => '{
"options": {
"floor_number": "Número de piso",
"type.name" : "Tipo de habitación",
"status": "Estado",
"": "Otro"
}
}',
                'type' => 'select_dropdown',
                'order' => 11,
                'group' => 'System',
            ),
        ));
        
        
    }
}