<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModuloNotificaciones extends Migration
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
            'asuntoNotificacion' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => false,
            ],
            'idTipoNotificacion' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
            ],
            'idRadicado' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
            ],
            'idEstadoNotificacion' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
            ],
            'idEstadoNotificacionAgente' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'idFormato' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'idRequerimiento' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'idCategoria' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'idCampania' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'anioReporte' => [
                'type' => 'YEAR',
                'null' => true,
            ],
            'entidad' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'trimestre' => [
                'type' => 'SMALLINT',
                'null' => true,
            ],
            'plazo' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'hora' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'plazoExtendido' => [
                'type' => 'SMALLINT',
                'null' => true,
            ],
            'sector' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'textoNotificacion' => [
                'type' => 'LONGTEXT',
                'null' => false,
            ],
            'observaciones' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'adjunto' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'recordatorioEnviado' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ],
            'fechaEnvioMail' => [
                'type' => 'DATETIME',
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
        $this->forge->addForeignKey('idTipoNotificacion', 'tiponotificaciones', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idRadicado', 'radicados', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idEstadoNotificacion', 'estadonotificacion', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idEstadoNotificacionAgente', 'estadonotificacion', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idFormato', 'formatos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idRequerimiento', 'tiporequerimiento', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idCategoria', 'categoria', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idCampania', 'campania', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('modulonotificaciones');
        $this->db->query('ALTER TABLE modulonotificaciones MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_modulonotificaciones_id ON modulonotificaciones(id)');
        $this->db->query('CREATE INDEX idx_modulonotificaciones_idTipoNotificacion ON modulonotificaciones(idTipoNotificacion)');
        $this->db->query('CREATE INDEX idx_modulonotificaciones_idTipoNotificacionAgente ON modulonotificaciones(idEstadoNotificacionAgente)');
        $this->db->query('CREATE INDEX idx_modulonotificaciones_idRadicado ON modulonotificaciones(idRadicado)');
        $this->db->query('CREATE INDEX idx_modulonotificaciones_idEstadoNotificacion ON modulonotificaciones(idEstadoNotificacion)');
        $this->db->query('CREATE INDEX idx_modulonotificaciones_idFormato ON modulonotificaciones(idFormato)');
        $this->db->query('CREATE INDEX idx_modulonotificaciones_idRequerimiento ON modulonotificaciones(idRequerimiento)');
        $this->db->query('CREATE INDEX idx_modulonotificaciones_idCategoria ON modulonotificaciones(idCategoria)');
        $this->db->query('CREATE INDEX idx_modulonotificaciones_idCampania ON modulonotificaciones(idCampania)');
    }

    public function down()
    {
        //
    }
}
