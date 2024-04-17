<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Rol extends Entity
{
    protected $attributes = [
        'id'                => null,
        'nombre'            => null,
        'fechaCreacion'     => null,
        'fechaModificacion' => null,
    ];
    protected $datamap = [
        'id'        => 'id',
        'name'      => 'nombre',
        'createdAt' => 'fechaCreacion',
        'updatedAt' => 'fechaModificacion',
    ];
    protected $dates   = [];
    protected $casts   = [
        'id'                => 'integer',
        'nombre'            => 'string',
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
    public function getId()
    {
        return $this->attributes['id'];
    }
    public function setId(int $id)
    {
        $this->attributes['id'] = $id;
    }

}
