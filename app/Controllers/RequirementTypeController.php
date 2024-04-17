<?php

namespace App\Controllers;

use Exception;
use ReflectionException;
use App\Interfaces\ICRUD;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Libraries\SupportLogHandler;
use App\Models\{TipoRequerimientoModel};
use App\Models\ModuloNotificacionesModel;

class RequirementTypeController extends BaseController implements ICRUD
{
    use ResponseTrait;
    public function index()
    {
        
    }
    public function list()
    {
        $vars=(Object)$this->request->getVar();
        $perPage = $vars->perPage??25;
        $page    = $vars->page??1;
        $filter  = [];
        if(isset($vars->search) and !empty($vars->search)){
            $filter['nombre'] = $vars->search;
        }
        $requirementTypeModel= new TipoRequerimientoModel();
        $requirementTypeModel->select('tiporequerimiento.id,nombre as name,tiporequerimiento.estado as status,tiponotificaciones.nombreTipo as shippingTypeName,tiponotificaciones.id as shippingType')
                             ->join('tiponotificaciones','tiponotificaciones.id=tiporequerimiento.idTipoNotificaciones');
        if(!empty($filter))$requirementTypeModel->like($filter);
        $requirementTypeModel->orderBy('tiporequerimiento.id','ASC');
        $requirements=$requirementTypeModel->asObject()->paginate(intval($perPage), 'default', intVal($page));
        return $this->getResponse([
            'message'      => 'OK',
            'items'        => $requirements,
            'totalRecords' => $requirementTypeModel->pager->getTotal(),
            'totalPages'   => $requirementTypeModel->pager->getPageCount(),
        ]);
    }
    public function show($id)
    {
        try{
            $requirementTypeModel= new TipoRequerimientoModel();
            $requirementType=$requirementTypeModel->asObject()->find($id);
            if(empty($requirementType)){
                throw new Exception('No se encontro el tipo de requerimiento',1);
            }
            return $this->getResponse([
                'message' => 'OK',
                'requirementType' => $requirementType,
            ]);

        }catch(Exception $e){
            if($e->getCode()==1){
                return $this->getResponse([
                    'message' => $e->getMessage(),
                ],HTTP_NOT_FOUND);
            }
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function create()
    {
        try{
            $data=(object)$this->request->getVar(); 
            if(!isset($data->name)) throw new Exception('El nombre es requerido',1);
            if(!isset($data->shippingType)) throw new Exception('El tipo de envio es requerido',1);
            $requirementTypeModel= new TipoRequerimientoModel();
            $existFormat=$requirementTypeModel->where('nombre',$data->name)
                                              ->first();
            if(!empty($existFormat))throw new Exception('El tipo de requerimiento ya existe',1);
            $requirementTypeModel->save(['nombre'=>$data->name,'idTipoNotificaciones'=>$data->shippingType]);
            return $this->getResponse([
                'message' => 'Tipo de requerimiento creado correctamente',
            ]);
        }
        catch(Exception $e){
            $message=$e->getMessage();
            if($e->getCode()==1){
                $this->log->info($message);
                return $this->getResponse(['message' => $message],HTTP_BAD_REQUEST);
            }
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function update()
    {
        try{
            $vars=(Object)$this->request->getVar();
            if(!isset($vars->id)) throw new Exception('El id es requerido',1);
            $id=$vars->id;
            if(!isset($vars->name)) throw new Exception('El nombre es requerido',1); 
            $notificacionesModel= new ModuloNotificacionesModel();  
            $existNotification=$notificacionesModel->where('idRequerimiento',$id)
                                                 ->first();
            if(!empty($existNotification))throw new Exception('No se puede editar el tipo de requerimiento, esta siendo usado en notificaciones',1); 
            $requirementTypeModel= new TipoRequerimientoModel();
            $updateRequirement=$requirementTypeModel->find($id);
            if(empty($updateRequirement))throw new Exception('No se encontro el tipo de requerimiento',1);
            $existRequirement=$requirementTypeModel->where('nombre',$vars->name)
                                                   ->first();
            if(isset($existRequirement) and $existRequirement->id!=$id)throw new Exception('El tipo de requerimiento ya existe',1);
            $updateRequirement->name=$vars->name;
            $updateRequirement->shippingType=$vars->shippingType;
            $updateRequirement->status=$vars->status;
            $updateRequirement->updatedAt=date('Y-m-d H:i:s');
            $requirementTypeModel->save($updateRequirement);
            return $this->getResponse([
                'message' => 'Tipo de requerimiento actualizado correctamente',
            ]);
        }
        catch(Exception $e){
            $message=$e->getMessage();
            if($message=='There is no data to update.')$message='No hay datos para actualizar';
            if($e->getCode()==1 or $message=='No hay datos para actualizar'){
                $this->log->info($message);
                return $this->getResponse(['message' => $message],HTTP_BAD_REQUEST);
            }
            $this->log->error($e->getMessage());
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function delete($id)
    {
        try{ 
            $requirementTypeModel= new TipoRequerimientoModel();
            $deleteFormat=$requirementTypeModel->find($id);
            if(empty($deleteFormat))throw new Exception('No se encontro el tipo de requerimiento',1);
            $requirementTypeModel->delete($id);
            return $this->getResponse([
                'message' => 'Tipo de requerimiento eliminado correctamente',
            ]);
        }
        catch(Exception $e){
            $message=$e->getMessage();
            if($e->getCode()==1){
                $this->log->info($message);
                return $this->getResponse(['message' => $message],HTTP_BAD_REQUEST);
            }
            $this->log->error($e->getMessage());
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }

}
