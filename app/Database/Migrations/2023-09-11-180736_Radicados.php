<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Radicados extends Migration
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
            'radicado' => [
                'type' => 'BIGINT',
                'unsigned' => true,
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
        $this->forge->createTable('radicados');
        $this->db->query('ALTER TABLE radicados MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_radicados_id ON radicados(id)');
        $this->db->query('CREATE INDEX idx_radicados_radicado ON radicados(radicado)');
    }

    public function down()
    {
        //
    }
}
