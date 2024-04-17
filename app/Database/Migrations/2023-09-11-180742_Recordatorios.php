<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Recordatorios extends Migration
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
            'diasSemaforizacion' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'diasRecordatorioMail' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'diasRecordatorioSMS' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'plantillaSMS' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'plantillaMail' => [
                'type' => 'TEXT',
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
        $this->forge->createTable('recordatorios');
        $this->db->query('ALTER TABLE recordatorios MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        //
    }
}
