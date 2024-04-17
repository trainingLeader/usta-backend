<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Borradores extends Migration
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
                'null' => true,
            ],
            'idEstadoNotificacion' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
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
            'sector' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'textoNotificacion' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'adjunto' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'para' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'de' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'archivoMasivo' => [
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
        $this->forge->addForeignKey('idTipoNotificacion', 'tiponotificaciones', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idRadicado', 'radicados', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idEstadoNotificacion', 'estadonotificacion', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idFormato', 'formatos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idRequerimiento', 'tiporequerimiento', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idCategoria', 'categoria', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idCampania', 'campania', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('borradores');
        $this->db->query('ALTER TABLE borradores MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');

        $this->db->query('CREATE INDEX idx_borradores_id ON borradores(id)');
        $this->db->query('CREATE INDEX idx_borradores_idTipoNotificacion ON borradores(idTipoNotificacion)');
        $this->db->query('CREATE INDEX idx_borradores_idRadicado ON borradores(idRadicado)');
        $this->db->query('CREATE INDEX idx_borradores_idEstadoNotificacion ON borradores(idEstadoNotificacion)');
        $this->db->query('CREATE INDEX idx_borradores_idFormato ON borradores(idFormato)');
        $this->db->query('CREATE INDEX idx_borradores_idRequerimiento ON borradores(idRequerimiento)');
        $this->db->query('CREATE INDEX idx_borradores_idCategoria ON borradores(idCategoria)');
        $this->db->query('CREATE INDEX idx_borradores_idCampania ON borradores(idCampania)');
    }

    public function down()
    {
        //
    }
}
