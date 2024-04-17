<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Submodule extends Entity
{
    protected $attributes = [
        'id'                => null,
        'nombreSubmodulo'   => null,
        'fechaCreacion'     => null,
        'fechaModificacion' => null,
    ];
    protected $datamap = [
        'name'      => 'nombreSubmodulo',
        'createdAt' => 'fechaCreacion',
        'updatedAt' => 'fechaModificacion',
    ];
    protected $dates   = [];
    protected $casts   = [
        'id'                => 'integer',
        'nombreSubmodulo'   => 'string',
        'fechaCreacion'     => 'datetime',
        'fechaModificacion' => 'datetime',
    ];
    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }

}
