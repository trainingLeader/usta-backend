<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TipoNotificaciones extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nombreTipo' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
            ],
            'fechaCreacion' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'fechaModificacion' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);        
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('tiponotificaciones');
        $this->db->query('ALTER TABLE tiponotificaciones MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        //
    }
}
