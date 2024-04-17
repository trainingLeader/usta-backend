<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationViewModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'notificationview';
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
        'to',
        'idThread',
        'color',
        'message',
        'daysToEnd',
        'body',
        'status',
        'idStatus'
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
        $builder = $db->table('notificationview');
        $builder->select('
                id,
                caseNumber as "No.Radicado",
                IF(sendDate IS NULL,"---",sendDate) as "Fecha de envio",
                IF(reminderSent IS NULL,0,reminderSent) as "Gestionados",
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
            $builder->where('sendDate >=',$vars->range->startDate);
            $builder->where('sendDate <=',$vars->range->endDate.' 23:59:59');
        }
        $builder->orderBy('id','ASC');
        $builder->groupBy('caseNumber');
        $query=$builder->getCompiledSelect();
        return $query;
    }
}
