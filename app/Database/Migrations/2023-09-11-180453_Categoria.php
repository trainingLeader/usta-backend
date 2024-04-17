<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Categoria extends Migration
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
                'constraint' => 80,
            ],
            'estado' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->createTable('categoria');
        $this->db->query('ALTER TABLE categoria MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        //
    }
}
