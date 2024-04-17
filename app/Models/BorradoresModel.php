<?php

namespace App\Models;

use CodeIgniter\Model;

class BorradoresModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'borradores';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'para',
        'asuntoNotificacion',
        'tipoFormato',
        'entidad',
        'anioReporte',
        'trimestre',
        'plazo',
        'hora',
        'tipoRequerimiento',
        'categoria',
        'sector',
        'idTipoNotificacion',
        'idRadicado',
        'idEstadoNotificacion',
        'idFormato',
        'idRequerimiento',
        'idCategoria',
        'idCampania',
        'textoNotificacion',
        'adjunto',
        'archivoMasivo',
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