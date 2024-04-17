<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class RequirementType extends Entity
{
    protected $attributes = [
        'id'                   => null,
        'idTipoNotificaciones' => null,
        'nombre'               => null,
        'estado'               => null,//1 activo 0 inactivo
        'fechaCreacion'        => null,
        'fechaModificacion'    => null,
    ];
    protected $datamap = [
        'shippingType' => 'idTipoNotificaciones',
        'name'         => 'nombre',
        'status'       => 'estado',
        'createdAt'    => 'fechaCreacion',
        'updatedAt'    => 'fechaModificacion',
    ];
    protected $dates   = [];
    protected $casts   = [
        'id'                   => 'integer',
        'idTipoNotificaciones' => 'integer',
        'nombre'               => 'string',
        'estado'               => 'integer',
        'fechaCreacion'        => 'datetime',
        'fechaModificacion'    => 'datetime',
    ];
    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }
    public function setShippingType(int $shippingType)
    {
        $this->attributes['idTipoNotificaciones'] = $shippingType;
    }
    public function getShippingType()
    {
        return $this->attributes['idTipoNotificaciones'];
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
