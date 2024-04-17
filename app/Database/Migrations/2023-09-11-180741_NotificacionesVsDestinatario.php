<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NotificacionesVsDestinatario extends Migration
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
            'idDestinatario' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'idNotificaciones' => [
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
        $this->forge->addForeignKey('idNotificaciones', 'modulonotificaciones', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idDestinatario', 'destinatario', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notificacionesvsdestinatario');
        $this->db->query('ALTER TABLE destinatario MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_notificacionesvsdestinatario_id ON notificacionesvsdestinatario(id)');
        $this->db->query('CREATE INDEX idx_notificacionesvsdestinatario_idDestinatario ON notificacionesvsdestinatario(idDestinatario)');
        $this->db->query('CREATE INDEX idx_notificacionesvsdestinatario_idNotificaciones ON notificacionesvsdestinatario(idNotificaciones)');
    }

    public function down()
    {
        //
    }
}
