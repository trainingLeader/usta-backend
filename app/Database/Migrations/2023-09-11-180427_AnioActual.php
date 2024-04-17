<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AnioActual extends Migration
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
            'anio' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
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
        $this->forge->createTable('anio_actual');
        $this->db->query('ALTER TABLE anio_actual MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        //
    }
}
