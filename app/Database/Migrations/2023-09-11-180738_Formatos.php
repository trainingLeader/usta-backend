<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Formatos extends Migration
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
            'nombreFormato' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
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
        $this->forge->createTable('formatos');
        $this->db->query('ALTER TABLE formatos MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        //
    }
}
