<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PermisosGenericos extends Migration
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
            'nombrePermiso' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
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
        $this->forge->createTable('permisosgenericos');
        $this->db->query('ALTER TABLE permisosgenericos MODIFY fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        //
    }
}
