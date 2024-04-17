<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HiloRespuestaNotificacion extends Migration
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
            'idNotificacion' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'de' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'mensaje' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'para' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'posicion' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'default' => 1,
            ],
            'estado' => [
                'type' => 'SMALLINT',
                'default' => 0
            ],
            'estadoOnbase' => [
                'type' => 'SMALLINT',
                'default' => 0
            ],
            'DocumentHandleOnbase' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            
            'adjunto' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->createTable('hilorespuestanotificacion');
        $this->db->query('ALTER TABLE hilorespuestanotificacion MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_hilorespuestanotificacion_id ON hilorespuestanotificacion(id)');
        $this->db->query('CREATE INDEX idx_hilorespuestanotificacion_idNotificacion ON hilorespuestanotificacion(idNotificacion)');
        $this->db->query('CREATE INDEX idx_hilorespuestanotificacion_de ON hilorespuestanotificacion(de)');
        $this->db->query('CREATE INDEX idx_hilorespuestanotificacion_para ON hilorespuestanotificacion(para)');
        $this->db->query('CREATE INDEX idx_hilorespuestanotificacion_estado ON hilorespuestanotificacion(estado)');
        $this->db->query('CREATE INDEX idx_hilorespuestanotificacion_estadoOnbase ON hilorespuestanotificacion(estadoOnbase)');

    }

    public function down()
    {
        //
    }
}
