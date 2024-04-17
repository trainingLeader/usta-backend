<?php

namespace App\Models;

use CodeIgniter\Model;

class BlockchainModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'blockchain';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'idTipoNotificacion',
        'idHiloRespuesta',
        'idAuditoria',
        'hashGenerado',
        'zilliqa',
        'hashGenerado2',
        'zilliqa2',
        'fechaCreacion',
        'fechaModificacion',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fechaCreacion';
    protected $updatedField  = 'fechaModificacion';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getSLQForExcel(int $perPage, int $page,object $vars,object $range, array $filter=[]){
        $db = \Config\Database::connect();
        $builder = $db->table('blockchain');
        $builder->select('blockchain.id,
                          blockchain.fechaCreacion as "Fecha inicial",
                          radicados.radicado as "No. Radicado",
                          blockchain.hashGenerado as "1er Hash",
                          IF(zilliqa is not NULL,CONCAT("https://viewblock.io/zilliqa/tx/0x",zilliqa,"?network=testnet"),"") as "Boton 1er Hash",
                          zilliqa as "Ubicacion 1er Hash",
                          IF(blockchain.hashGenerado2 is not NULL,blockchain.hashGenerado2,"") as "2do Hash",
                          IF(zilliqa2 is not NULL,CONCAT("https://viewblock.io/zilliqa/tx/0x",zilliqa2,"?network=testnet"),"") as "Boton 2do Hash",
                          IF(zilliqa2 is not NULL,zilliqa2,"") as "Ubicacion 2do Hash",
                          IF(modulonotificaciones.entidad is not NULL,modulonotificaciones.entidad,"") as "Entidad/Agente",
                          hilorespuestanotificacion.posicion as "Posicion",
                          IF(hilorespuestanotificacion.estado=1,"Respondida","Sin respuesta") as "Estado",
                          IF(hilorespuestanotificacion.estado=0,DATEDIFF(NOW(),hilorespuestanotificacion.fechaCreacion),"--") as "Dias sin respuesta"');
        $builder->join('hilorespuestanotificacion','hilorespuestanotificacion.id=blockchain.idHiloRespuesta')
                ->join('modulonotificaciones','modulonotificaciones.id=hilorespuestanotificacion.idNotificacion')
                ->join('radicados','radicados.id=modulonotificaciones.idRadicado');
        if(isset($vars->email) and !empty($vars->email)){
            $builder->groupStart()
                        ->where('hilorespuestanotificacion.para',$vars->email)
                        ->orWhere('hilorespuestanotificacion.de',$vars->email)
                    ->groupEnd();
        }
        if(!empty($filter))$builder->where($filter);

        if(isset($range->startDate) and $range->startDate!="" and isset($range->endDate) and $range->endDate!=""){
            //usar between
            $builder->where('modulonotificaciones.plazo >=',$range->startDate);
            $builder->where('modulonotificaciones.plazo <=',$range->endDate.' 23:59:59');
        }
        $builder->orderBy('modulonotificaciones.idRadicado','ASC');
        $query=$builder->getCompiledSelect();
        return $query;
    }
}
