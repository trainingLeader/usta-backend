<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HistorialCambios extends Migration
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
            'idBorrador' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'idNotificacion' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'tipo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
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
        $this->forge->createTable('historialcambios');
        $this->db->query('ALTER TABLE historialcambios MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_historialcambios_id ON historialcambios(id)');
        $this->db->query('CREATE INDEX idx_historialcambios_idBorrador ON historialcambios(idBorrador)');
        $this->db->query('CREATE INDEX idx_historialcambios_idNotificacion ON historialcambios(idNotificacion)');
    }

    public function down()
    {
        //
    }
}
