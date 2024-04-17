<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nombre' => 'Super Administrador',
            ],
            [
                'nombre' => 'Configuración',
            ],
            [
                'nombre' => 'Notificaciones Creador',
            ],
            [
                'nombre' => 'Notificaciones Revisor',
            ],
            [
                'nombre' => 'Aprobador',
            ],
            [
                'nombre' => 'Agente',
            ],
        ];

        // Using Query Builder
        $this->db->table('rol')->insertBatch($data);
        $data=[
            [
                'nombrePermiso' => 'Crear notificación',
            ],
            [
                'nombrePermiso' => 'Eliminar',
            ],
            [
                'nombrePermiso' => 'Actualizar',
            ],
            [
                'nombrePermiso' => 'Consultar',
            ],
            [
                'nombrePermiso' => 'Inactivar',
            ],
            [
                'nombrePermiso' => 'Enviar',
            ],
            [
                'nombrePermiso' => 'Responder',
            ],
            [
                'nombrePermiso' => 'Editar semaforización',
            ],
            [
                'nombrePermiso' => 'Retornar a bandeja',
            ],
            [
                'nombrePermiso' => 'Guardar',
            ],
            [
                'nombrePermiso' => 'Archivar',
            ]
         
        ];
        $this->db->table('permisosgenericos')->insertBatch($data);
        $data=[
            ['id'=>1,'nombreModulo' => 'Gestión de notificaciones',],
            ['id'=>2,'nombreModulo' => 'Histórico Blockchain',],
            ['id'=>3,'nombreModulo' => 'Reportes',],
            ['id'=>4,'nombreModulo' => 'Configuración',],
        ];
        $this->db->table('modulosmaestros')->insertBatch($data);

        $data=[
            ['id'=>1,'nombreSubmodulo'=>'Principal'],
            ['id'=>2,'nombreSubmodulo'=>'Enviadas'],
            ['id'=>3,'nombreSubmodulo'=>'Archivadas'],
            ['id'=>4,'nombreSubmodulo'=>'Intercambio de notificaciones'],
            ['id'=>5,'nombreSubmodulo'=>'Notificaciones'],
            ['id'=>6,'nombreSubmodulo'=>'Notificaciones Masivas'],
            ['id'=>7,'nombreSubmodulo'=>'Mail y SMS masivas'],
            ['id'=>8,'nombreSubmodulo'=>'Tipo de formato',],
            ['id'=>9,'nombreSubmodulo'=>'Tipo de requerimiento',],
            ['id'=>10,'nombreSubmodulo'=>'Categoría',],
            ['id'=>11,'nombreSubmodulo'=>'Recordatorios',],
            ['id'=>12,'nombreSubmodulo'=>'Roles'],
            ['id'=>13,'nombreSubmodulo'=>'Por procesar'],

        ];
        $this->db->table('submodulos')->insertBatch($data);
        $data=[
            ['idMaestro'=>1,'idSubmodulo'=>1],
            ['idMaestro'=>1,'idSubmodulo'=>2],
            ['idMaestro'=>1,'idSubmodulo'=>3],
            ['idMaestro'=>2,'idSubmodulo'=>4],
            ['idMaestro'=>3,'idSubmodulo'=>5],
            ['idMaestro'=>3,'idSubmodulo'=>6],
            ['idMaestro'=>3,'idSubmodulo'=>7],
            ['idMaestro'=>4,'idSubmodulo'=>8,],
            ['idMaestro'=>4,'idSubmodulo'=>9,],
            ['idMaestro'=>4,'idSubmodulo'=>10,],
            ['idMaestro'=>4,'idSubmodulo'=>11,],
            ['idMaestro'=>4,'idSubmodulo'=>12],
            ['idMaestro'=>1,'idSubmodulo'=>13],
        ];
        $this->db->table('maestrosvssubmodulos')->insertBatch($data);
        $data=[
            [
                'id' => 1,
                'nombreEstado' => 'Recibidas',
            ],
            [
                'id' => 2,
                'nombreEstado' => 'Enviadas',
            ],
            [
                'id' => 3,
                'nombreEstado' => 'Archivadas',
            ],
            [
                'id' => 4,
                'nombreEstado' => 'Por procesar',
            ],
            [
                'id' => 5,
                'nombreEstado' => 'Cerrada',
            ]
        ];
        $this->db->table('estadonotificacion')->insertBatch($data);
    }
}
