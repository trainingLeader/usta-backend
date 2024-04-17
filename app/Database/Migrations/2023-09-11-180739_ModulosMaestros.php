<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModulosMaestros extends Migration
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
            'nombreModulo' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->createTable('modulosmaestros');
        $this->db->query('ALTER TABLE modulosmaestros MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_modulosmaestros_id ON modulosmaestros(id)');
        $this->db->query('CREATE INDEX idx_modulosmaestros_nombreModulo ON modulosmaestros(nombreModulo)');

    }

    public function down()
    {
        //
    }
}
