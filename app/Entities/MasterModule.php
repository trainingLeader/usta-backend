<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class MasterModule extends Entity
{
    protected $attributes = [
        'id'                => null,
        'nombreModulo'      => null,
        'fechaCreacion'     => null,
        'fechaModificacion' => null,
    ];
    protected $datamap = [
        'name'      => 'nombreModulo',
        'createdAt' => 'fechaCreacion',
        'updatedAt' => 'fechaModificacion',
    ];
    protected $dates   = [];
    protected $casts   = [
        'id'                => 'integer',
        'nombreModulo'      => 'string',
        'fechaCreacion'     => 'datetime',
        'fechaModificacion' => 'datetime',
    ];
    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }

}
