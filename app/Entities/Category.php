<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Category extends Entity
{
    protected $attributes = [
        'id'                => null,
        'nombre'            => null,
        'estado'            => null,//1 activo 0 inactivo
        'fechaCreacion'     => null,
        'fechaModificacion' => null,
    ];
    protected $datamap = [
        'name'      => 'nombre',
        'status'    => 'estado', 
        'createdAt' => 'fechaCreacion',
        'updatedAt' => 'fechaModificacion',
    ];
    protected $dates   = [];
    protected $casts   = [
        'id'                => 'integer',
        'nombre'            => 'string',
        'estado'            => 'integer',
        'fechaCreacion'     => 'datetime',
        'fechaModificacion' => 'datetime',
    ];
    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }
    public function setName(string $name)
    {
        $this->attributes['nombre'] = $name;
    }
    public function getName()
    {
        return $this->attributes['nombre'];
    }
    public function setStatus(int $status)
    {
        $this->attributes['estado'] = $status;
    }
    public function getStatus()
    {
        return $this->attributes['estado'];
    }
    public function setCreatedAt(string $createdAt)
    {
        $this->attributes['fechaCreacion'] = $createdAt;
    }
    public function getCreatedAt()
    {
        return $this->attributes['fechaCreacion'];
    }
    public function setUpdatedAt(string $updatedAt)
    {
        $this->attributes['fechaModificacion'] = $updatedAt;
    }
    public function getUpdatedAt()
    {
        return $this->attributes['fechaModificacion'];
    }

}
