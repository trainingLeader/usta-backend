<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Campania extends Migration
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
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
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
        $this->forge->createTable('campania');
        $this->db->query('ALTER TABLE campania MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        //
    }
}
