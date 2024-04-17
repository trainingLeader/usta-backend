<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class GenericosVsSubmodulos extends Entity
{
    protected $attributes = [
        'id'           => null,
        'idSubmodulos' => null,
        'idGenericos'  => null,
        'fechaCreacion'     => null,
        'fechaModificacion' => null,
        
    ];
    protected $datamap = [
        'id'           => 'id',
        'idSubmodulos' => 'idSubmodulos',
        'idGenericos'  => 'idGenericos',
        'createdAt'    => 'fechaCreacion',
        'updatedAt'    => 'fechaModificacion',
    ];
    protected $dates   = [];
    protected $casts   = [
        'id'                => 'integer',
        'idSubmodulos'      => 'integer',
        'idGenericos'       => 'integer',
        'fechaCreacion'     => 'datetime',
        'fechaModificacion' => 'datetime',
    ];
    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }

}
