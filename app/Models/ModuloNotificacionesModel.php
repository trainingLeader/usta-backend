<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuloNotificacionesModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'modulonotificaciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'asuntoNotificacion',
        'idTipoNotificacion',
        'idRadicado',
        'idEstadoNotificacion',
        'idEstadoNotificacionAgente',
        'idFormato',
        'idRequerimiento',
        'idCategoria',
        'idCampania',
        'anioReporte',
        'entidad',
        'trimestre',
        'plazo',
        'hora',
        'plazoExtendido',
        'sector',
        'textoNotificacion',
        'observaciones',
        'adjunto',
        'recordatorioEnviado',
        'fechaEnvioMail',
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
}
