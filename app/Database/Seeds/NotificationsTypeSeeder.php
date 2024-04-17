<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationsTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'=>5,
                'nombreTipo' => 'Unico',
            ],
            [
                'id'=>6,
                'nombreTipo' => 'Masivo',
            ],
        ];

        // Using Query Builder
        $this->db->table('tiponotificaciones')->insertBatch($data);
        $data = 
        [
            'id'=>1,
            'anio' => date('Y'),
        ];
        $this->db->table('anio_actual')->insert($data);
    }
}
