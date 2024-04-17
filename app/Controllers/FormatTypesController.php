<?php

namespace App\Controllers;

use Exception;
use ReflectionException;
use App\Interfaces\ICRUD;
use App\Models\{FormatosModel};
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Libraries\SupportLogHandler;
use App\Models\ModuloNotificacionesModel;

class FormatTypesController extends BaseController implements ICRUD
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
            $filter['nombreFormato'] = $vars->search;
        }
        $formaTypesModel= new FormatosModel();
        $formaTypesModel->select('id,nombreFormato as name, estado as status');
        if(!empty($filter))$formaTypesModel->like($filter);
        $formaTypesModel->orderBy('id','ASC');
        $formats=$formaTypesModel->asObject()->paginate(intval($perPage), 'default', intVal($page));
        return $this->getResponse([
            'message'      => 'OK',
            'items'        => $formats,
            'totalRecords' => $formaTypesModel->pager->getTotal(),
            'totalPages'   => $formaTypesModel->pager->getPageCount(),
        ]);
    }
    public function show($id)
    {
        try{
            $formaTypesModel= new FormatosModel();
            $formatType=$formaTypesModel->asObject()->find($id);
            if(empty($formatType)){
                throw new Exception('No se encontro el tipo de formato',1);
            }
            return $this->getResponse([
                'message' => 'OK',
                'formatType' => $formatType,
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
            $formaTypesModel= new FormatosModel();
            $existFormat=$formaTypesModel->where('nombreFormato',$data->name)
                          ->first();
            if(!empty($existFormat))throw new Exception('El tipo de formato ya existe',1);
            $formaTypesModel->save(['nombreFormato'=>$data->name]);
            return $this->getResponse([
                'message' => 'Tipo de formato creado correctamente',
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
            $existNotification=$notificacionesModel->where('idFormato',$id)
                                                 ->first();
            if(!empty($existNotification))throw new Exception('No se puede editar el formato, esta siendo usado en notificaciones',1); 
            $formaTypesModel= new FormatosModel();
            $updateFormat=$formaTypesModel->find($id);
            if(empty($updateFormat))throw new Exception('No se encontro el tipo de formato',1);
            $existFormat=$formaTypesModel->where('nombreFormato',$vars->name)
                                         ->first();
            if(isset($existFormat) and $existFormat->id!=$id)throw new Exception('El tipo de formato ya existe',1);
            $updateFormat->name=$vars->name;
            $updateFormat->status=$vars->status;
            $updateFormat->updatedAt=date('Y-m-d H:i:s');
            $formaTypesModel->save($updateFormat);
            return $this->getResponse([
                'message' => 'Tipo de formato actualizado correctamente',
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
            $formaTypesModel= new FormatosModel();
            $deleteFormat=$formaTypesModel->find($id);
            if(empty($deleteFormat))throw new Exception('No se encontro el tipo de formato',1);
            $formaTypesModel->delete($id);
            return $this->getResponse([
                'message' => 'Tipo de formato eliminado correctamente',
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
