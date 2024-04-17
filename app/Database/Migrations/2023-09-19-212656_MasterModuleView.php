<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterModuleView extends Migration
{
    public function up()
    {
        $this->db->query('
            CREATE OR REPLACE VIEW mastermoduleview AS
            SELECT 
                modulosmaestros.id AS id,
                modulosmaestros.nombreModulo AS name
            FROM
                modulosmaestros
        ');
        
    }

    public function down()
    {
        //
    }
}
