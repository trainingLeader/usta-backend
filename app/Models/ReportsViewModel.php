<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportsViewModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'reportsview';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'caseNumber',
        'subject',
        'deadline',
        'hour',
        'entity',
        'formatType',
        'requirementType',
        'year',
        'quarter',
        'category',
        'sector',
        'createdDocument',
        'from',
        'to',
        'idThread',
        'color',
        'message',
        'daysToEnd',
        'body',
        'status',
        'idStatus',
        'sendDate',
        'reminderSent',
        'idCampain',
        'campain',
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

    public function getSLQForExcelReports(int $perPage, int $page,object $vars,object $range, array $filter=[]){
        $db = \Config\Database::connect();
        $builder = $db->table('reportsview');
        $builder->select('
        reportsview.`id` AS "No.",
        reportsview.`from` AS "Responsable",
        IF(reportsview.campain IS NULL, "(N/A)", reportsview.campain) AS "Campaña",
        reportsview.`from` AS "Operador",
        (CASE
            WHEN reportsview.color = "Verde" THEN "A tiempo"
            WHEN reportsview.color = "Amarillo" THEN "Próximo a vencer"
            WHEN reportsview.color = "Rojo" THEN "Vencido"
            WHEN reportsview.color = "Morado" THEN "Notificación cerrada"
            ELSE ""
        END) AS "Estado de la solicitud",
        reportsview.`caseNumber` AS "Radicado",
        reportsview.`deadline` AS "Plazo",
        reportsview.`observations` AS "Observaciones",
        COALESCE((
            SELECT resp.dateResponse
            FROM reportsview resp
            WHERE resp.caseNumber = reportsview.caseNumber
              AND resp.`from` = reportsview.`to`
              AND resp.`to` = reportsview.`from`
              ORDER BY  id ASC
            LIMIT 1
        ), "(N/A)") AS "Recibido"
            ');
            if(!empty($filter))$builder->where($filter);
            if(isset($vars->search) and !empty($vars->search)){
                $builder->groupStart();
                $builder->like('subject',$vars->search);
                $builder->orLike('body' ,$vars->search);
                $builder->groupEnd();
            }
            if(isset($vars->email) and !empty($vars->email)){
                $builder->where('to',$vars->email);
            }
            if(isset($vars->status) and !empty($vars->status) and !is_array($vars->status)){
                $builder->where('status',$vars->status);
            }
            if(isset($vars->status) and !empty($vars->status) and is_array($vars->status)){
                $builder->whereIn('status',$vars->status);
            }
            //rango de fechas
            if(isset($vars->range))
            {
                $vars->range=(Object)$vars->range;
            }
            if(isset($vars->range->startDate) and $vars->range->startDate!="" and isset($vars->range->endDate) and $vars->range->endDate!=""){
                //usar between
                $builder->where('deadline >=',$vars->range->startDate);
                $builder->where('deadline <=',$vars->range->endDate.' 23:59:59');
            }
            $builder->groupBy('caseNumber');
            $builder->orderBy('id','ASC');
        $query=$builder->getCompiledSelect();
        return $query;
    }
    public function getSLQForExcel(int $perPage, int $page,object $vars,object $range, array $filter=[]){
        $db = \Config\Database::connect();
        $builder = $db->table('reportsview');
        $stringSelect = 'reportsview.id as No,';
        if(isset($filter['entity'] ) ){
            $stringSelect .= 'reportsview.`entity` AS "Entidad",';
        }
        if(isset($filter['formatType'])){
            $stringSelect .= 'reportsview.`nombreFormato` AS "Tipo de formato",';
        }
        if(isset($filter['requirementType'] )){
            $stringSelect .= 'tiporequerimiento.`nombre` AS "Tipo de requerimiento",';
        }
        if(isset($filter['category']) ){
            $stringSelect .= 'categoria.`nombre` AS "Categoria",';
        }
        if(isset($filter['sector']) ){
            $stringSelect .= 'reportsview.`sector` AS "Sector",';
        }
        if(isset($filter['idStatus'])){
            $stringSelect .= 'estadonotificacion.`nombreEstado` AS "Estado",';
        }
        if(isset($filter['year']) ){
            $stringSelect .= 'reportsview.`year` AS "Año de reporte",';
        }
        if(isset($filter['quarter'] ) ){
            $stringSelect .= 'reportsview.`quarter` AS "Trimestre",';
        }
        if(isset($filter['deadline'] )){
            $stringSelect .= 'reportsview.`deadline` AS "Plazo",';
        }
        if(isset($filter['hour'])){
            $stringSelect .= 'reportsview.`hour` AS "Hora",';
        }
        $stringSelect .= '
                caseNumber as "No.Radicado",
                IF(sendDate IS NULL,"---",sendDate) as "Fecha de envio",
                IF(reminderSent IS NULL,0,reminderSent) as "Gestionados",
        ';
        $builder->select($stringSelect);
        if(isset($filter['entity'] ) ){
            $stringSelect .= 'reportsview.`entity` AS "Entidad",';
        }
        if(isset($filter['formatType'])){
            $builder->join('formatos','formatos.id=reportsview.formatType');
        }
        if(isset($filter['requirementType'] )){
            $builder->join('tiporequerimiento','tiporequerimiento.id=reportsview.requirementType');
        }
        if(isset($filter['category']) ){
            $builder->join('categoria','categoria.id=reportsview.category');
        }
        /* if(isset($filter['sector']) ){
            $stringSelect .= 'reportsview.`sector` AS "Sector",';
        } */
        if(isset($filter['idStatus'])){
            $builder->join('estadonotificacion','estadonotificacion.id=reportsview.idStatus');
        }/* 
        if(isset($filter['year']) ){
            $stringSelect .= 'reportsview.`year` AS "Año de reporte",';
        }
        if(isset($filter['quarter'] ) ){
            $stringSelect .= 'reportsview.`quarter` AS "Trimestre",';
        }
        if(isset($filter['deadline'] )){
            $stringSelect .= 'reportsview.`deadline` AS "Plazo",';
        }
        if(isset($filter['hour'])){
            $stringSelect .= 'reportsview.`hour` AS "Hora",';
        } */
        
        if(!empty($filter))$builder->where($filter);
        if(isset($vars->search) and !empty($vars->search)){
            $builder->groupStart();
            $builder->like('subject',$vars->search);
            $builder->orLike('body' ,$vars->search);
            $builder->groupEnd();
        }
        if(isset($vars->email) and !empty($vars->email)){
            $builder->where('to',$vars->email);
        }
        if(isset($vars->status) and !empty($vars->status) and !is_array($vars->status)){
            $builder->where('status',$vars->status);
        }
        if(isset($vars->status) and !empty($vars->status) and is_array($vars->status)){
            $builder->whereIn('status',$vars->status);
        }
        //rango de fechas
        if(isset($vars->range))
        {
            $vars->range=(Object)$vars->range;
        }
        if(isset($vars->range->startDate) and $vars->range->startDate!="" and isset($vars->range->endDate) and $vars->range->endDate!=""){
            //usar between
            $builder->where('sendDate >=',$vars->range->startDate);
            $builder->where('sendDate <=',$vars->range->endDate.' 23:59:59');
        }
        $builder->orderBy('reportsview.id','ASC');
        $builder->groupBy('caseNumber');
        $query=$builder->getCompiledSelect();
        return $query;
    }
    public function getTotalAgents(int $perPage, int $page,object $vars,object $range, array $filter=[]){
        $db = \Config\Database::connect();
        $builder = $db->table('reportsview');
        $builder->select('
                id,`to`
        ');
        if(!empty($filter))$builder->where($filter);
        if(isset($vars->search) and !empty($vars->search)){
            $builder->groupStart();
            $builder->like('subject',$vars->search);
            $builder->orLike('body' ,$vars->search);
            $builder->groupEnd();
        }
        if(isset($vars->email) and !empty($vars->email)){
            $builder->where('to',$vars->email);
        }
        if(isset($vars->status) and !empty($vars->status) and !is_array($vars->status)){
            $builder->where('status',$vars->status);
        }
        if(isset($vars->status) and !empty($vars->status) and is_array($vars->status)){
            $builder->whereIn('status',$vars->status);
        }
        //rango de fechas
        if(isset($vars->range))
        {
            $vars->range=(Object)$vars->range;
        }
        if(isset($vars->range->startDate) and $vars->range->startDate!="" and isset($vars->range->endDate) and $vars->range->endDate!=""){
            //usar between
            $builder->where('deadline >=',$vars->range->startDate);
            $builder->where('deadline <=',$vars->range->endDate.' 23:59:59');
        }
        $builder->orderBy('id','ASC');
        $builder->groupBy('`to`');
        $table1=$builder->getCompiledSelect();
        $table2 = $db->table("({$table1}) as t1")
                    ->select('
                        t1.id, t1.`to`,
                        ROW_NUMBER() OVER(PARTITION BY id ORDER BY id ASC) AS rn
                    ');
        $table2 = $table2->getCompiledSelect();
        $query = $db->table("({$table2}) as t2")
                    ->select('
                        COUNT(*) as TotalAgents
                    ')
                    ->where('rn',1);
        $total = $query->get()->getResultArray();
        $total=current($total);
        return $total['TotalAgents'];
    }
}
