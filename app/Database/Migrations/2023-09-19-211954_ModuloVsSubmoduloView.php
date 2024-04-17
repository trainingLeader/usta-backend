<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModuloVsSubmoduloView extends Migration
{
    public function up()
    {
        $this->db->query('
            CREATE OR REPLACE VIEW modulovssubmoduloview AS
            SELECT
                maestrosvssubmodulos.id AS idModuloVsSubmodulo,
                maestrosvssubmodulos.idMaestro AS idModulo,
                modulosmaestros.nombreModulo AS nombreModulo,
                maestrosvssubmodulos.idSubmodulo AS idSubmodulo,
                submodulos.nombreSubmodulo AS nombreSubmodulo
            FROM 
                maestrosvssubmodulos
            INNER JOIN modulosmaestros ON maestrosvssubmodulos.idMaestro = modulosmaestros.id
            INNER JOIN submodulos ON maestrosvssubmodulos.idSubmodulo = submodulos.id            
        ');

    }

    public function down()
    {
        //
    }
}
