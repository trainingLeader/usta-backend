<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RolVsMaestro extends Migration
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
            'idRol' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
            ],
            'idMaestro' => [
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
        $this->forge->addForeignKey('idRol', 'rol', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idMaestro', 'modulosmaestros', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rolvsmaestro');
        $this->db->query('ALTER TABLE rolvsmaestro MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_rolvsmaestro_id ON rolvsmaestro(id)');
        $this->db->query('CREATE INDEX idx_rolvsmaestro_idRol ON rolvsmaestro(idRol)');
        $this->db->query('CREATE INDEX idx_rolvsmaestro_idMaestro ON rolvsmaestro(idMaestro)');
    }

    public function down()
    {
        //
    }
}
