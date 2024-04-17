<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Notification extends Entity
{
    protected $attributes = [
        'id'                  => null,
        'asuntoNotificacion'  => null,
        'idTipoNotificacion'  => null,
        'idRadicado'          => null,
        'idEstadoNotificacion'=> null,
        'idFormato'           => null,
        'idRequerimiento'     => null,
        'idCategoria'         => null,
        'idCampania'          => null,
        'anioReporte'         => null,
        'entidad'             => null,
        'trimestre'           => null,
        'plazo'               => null,
        'hora'                => null,
        'sector'              => null,
        'textoNotificacion'   => null,
        'fechaCreacion'       => null,
        'fechaModificacion'   => null,
    ];
    protected $datamap = [
        'subject'             => 'asuntoNotificacion',
        'idNotificationType'  => 'idTipoNotificacion',
        'caseNumber'          => 'idRadicado',
        'idNotificationState' => 'idEstadoNotificacion',
        'idformat'            => 'idFormato',
        'idrequirement'       => 'idRequerimiento',
        'idcategory'          => 'idCategoria',
        'idcampaign'          => 'idCampania',
        'reportYear'          => 'anioReporte',
        'entity'              => 'entidad',
        'trimester'           => 'trimestre',
        'deadline'            => 'plazo',
        'hour'                => 'hora',
        'sector'              => 'sector',
        'notificationText'    => 'textoNotificacion',
        'createdAt'           => 'fechaCreacion',
        'updatedAt'           => 'fechaModificacion',
    ];
    protected $dates   = [];
    protected $casts   = [
        'id'                => 'integer',
        'asuntoNotificacion'=> 'string',
        'idTipoNotificacion'=> 'integer',
        'caseNumber'        => 'integer',
        'idEstadoNotificacion'=> 'integer',
        'idFormato'         => 'integer',
        'idRequerimiento'   => 'integer',
        'idCategoria'       => 'integer',
        'idCampania'        => 'integer',
        'anioReporte'       => 'integer',
        'entidad'           => 'string',
        'trimestre'         => 'integer',
        'plazo'             => 'date',
        'hora'              => 'string',
        'sector'            => 'string',
        'textoNotificacion' => 'string',
        'fechaCreacion'     => 'datetime',
        'fechaModificacion' => 'datetime',
    ];
    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }
    public function setSubject(string $subject)
    {
        $this->attributes['asuntoNotificacion'] = $subject;
    }
    public function getSubject()
    {
        return $this->attributes['asuntoNotificacion'];
    }
    public function setIdNotificationType(int $idNotificationType)
    {
        $this->attributes['idTipoNotificacion'] = $idNotificationType;
    }
    public function getIdNotificationType()
    {
        return $this->attributes['idTipoNotificacion'];
    }
    public function setCaseNumber(int $idRadicated)
    {
        $this->attributes['idRadicado'] = $idRadicated;
    }
    public function getCaseNumber()
    {
        return $this->attributes['idRadicado'];
    }
    public function setIdNotificationState(int $idNotificationState)
    {
        $this->attributes['idEstadoNotificacion'] = $idNotificationState;
    }
    public function getIdNotificationState()
    {
        return $this->attributes['idEstadoNotificacion'];
    }
    public function setIdFormat(int $idFormat)
    {
        $this->attributes['idFormato'] = $idFormat;
    }
    public function getIdFormat()
    {
        return $this->attributes['idFormato'];
    }
    public function setIdRequirement(int $idRequirement)
    {
        $this->attributes['idRequerimiento'] = $idRequirement;
    }
    public function getIdRequirement()
    {
        return $this->attributes['idRequerimiento'];
    }
    public function setIdCategory(int $idCategory)
    {
        $this->attributes['idCategoria'] = $idCategory;
    }
    public function getIdCategory()
    {
        return $this->attributes['idCategoria'];
    }
    public function setIdCampaign(int $idCampaign)
    {
        $this->attributes['idCampania'] = $idCampaign;
    }
    public function getIdCampaign()
    {
        return $this->attributes['idCampania'];
    }
    public function setReportYear(int $reportYear)
    {
        $this->attributes['anioReporte'] = $reportYear;
    }
    public function getReportYear()
    {
        return $this->attributes['anioReporte'];
    }
    public function setEntity(string $entity)
    {
        $this->attributes['entidad'] = $entity;
    }
    public function getEntity()
    {
        return $this->attributes['entidad'];
    }
    public function setTrimester(int $trimester)
    {
        $this->attributes['trimestre'] = $trimester;
    }
    public function getTrimester()
    {
        return $this->attributes['trimestre'];
    }
    public function setDeadline(string $deadline)
    {
        $this->attributes['plazo'] = $deadline;
    }
    public function getDeadline()
    {
        return $this->attributes['plazo'];
    }
    public function setHour(string $hour)
    {
        $this->attributes['hora'] = $hour;
    }
    public function getHour()
    {
        return $this->attributes['hora'];
    }
    public function setSector(string $sector)
    {
        $this->attributes['sector'] = $sector;
    }
    public function getSector()
    {
        return $this->attributes['sector'];
    }
    public function setNotificationText(string $notificationText)
    {
        $this->attributes['textoNotificacion'] = $notificationText;
    }
    public function getNotificationText()
    {
        return $this->attributes['textoNotificacion'];
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
