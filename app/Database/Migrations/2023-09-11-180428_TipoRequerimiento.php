<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TipoRequerimiento extends Migration
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
            'idTipoNotificaciones' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
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
        $this->forge->addForeignKey('idTipoNotificaciones', 'tiponotificaciones', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tiporequerimiento');
        $this->db->query('ALTER TABLE tiporequerimiento MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_tiporequerimiento_id ON tiporequerimiento(id)');
        $this->db->query('CREATE INDEX idx_tiporequerimiento_idTipoNotificaciones ON tiporequerimiento(idTipoNotificaciones)');
    }

    public function down()
    {
        //
    }
}
