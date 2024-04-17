<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GenericosVsSubmodulos extends Migration
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
            'idGenericos' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'idSubmodulos' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'idRol' => [
                'type' => 'BIGINT',
                'constraint' => 20,
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
        $this->forge->addForeignKey('idGenericos', 'permisosgenericos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idSubmodulos', 'maestrosvssubmodulos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idRol', 'rol', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('genericosvssubmodulos');
        $this->db->query('ALTER TABLE genericosvssubmodulos MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_genericosvssubmodulos_id ON genericosvssubmodulos(id)');
        $this->db->query('CREATE INDEX idx_genericosvssubmodulos_idGenericos ON genericosvssubmodulos(idGenericos)');
        $this->db->query('CREATE INDEX idx_genericosvssubmodulos_idSubmodulos ON genericosvssubmodulos(idSubmodulos)');
        $this->db->query('CREATE INDEX idx_genericosvssubmodulos_idRol ON genericosvssubmodulos(idRol)');
    }

    public function down()
    {
        //
    }
}
