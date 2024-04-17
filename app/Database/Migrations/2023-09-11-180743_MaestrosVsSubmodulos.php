<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MaestrosVsSubmodulos extends Migration
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
            'idMaestro' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
            ],
            'idSubmodulo' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
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
        $this->forge->addForeignKey('idMaestro', 'modulosmaestros', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idSubmodulo', 'submodulos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('maestrosvssubmodulos');
        $this->db->query('ALTER TABLE maestrosvssubmodulos MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_maestrosvssubmodulos_id ON maestrosvssubmodulos(id)');
        $this->db->query('CREATE INDEX idx_maestrosvssubmodulos_idMaestro ON maestrosvssubmodulos(idMaestro)');
        $this->db->query('CREATE INDEX idx_maestrosvssubmodulos_idSubmodulo ON maestrosvssubmodulos(idSubmodulo)');
    }

    public function down()
    {
        //
    }
}
