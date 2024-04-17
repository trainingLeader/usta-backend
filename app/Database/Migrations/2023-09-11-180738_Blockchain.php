<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Blockchain extends Migration
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
            'idTipoNotificacion' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'idHiloRespuesta' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'idAuditoria' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'hashGenerado' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'zilliqa' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'hashGenerado2' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'zilliqa2' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
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
        $this->forge->addForeignKey('idHiloRespuesta', 'hilorespuestanotificacion', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idAuditoria', 'auditoria', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blockchain');
        $this->db->query('ALTER TABLE blockchain MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
        //indexar
        $this->db->query('CREATE INDEX idx_blockchain_id ON blockchain(id)');
        $this->db->query('CREATE INDEX idx_blockchain_idTipoNotificacion ON blockchain(idTipoNotificacion)');
        $this->db->query('CREATE INDEX idx_blockchain_idHiloRespuesta ON blockchain(idHiloRespuesta)');
        $this->db->query('CREATE INDEX idx_blockchain_idAuditoria ON blockchain(idAuditoria)');
    }

    public function down()
    {
        //
    }
}
