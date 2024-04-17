<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Interfaces\ICRUD;
use Exception;
use ReflectionException;
use App\Models\{BorradoresModel};
use App\Libraries\SupportLogHandler;
class DraftController extends BaseController
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
        $draftModel= new BorradoresModel();
        $draftModel->select('*');
        if(!empty($filter))$draftModel->where($filter);
        $draftModel->orderBy('id','ASC');
        $formats=$draftModel->asObject()->paginate(intval($perPage), 'default', intVal($page));
        return $this->getResponse([
            'message'      => 'OK',
            'items'        => $formats,
            'totalRecords' => $draftModel->pager->getTotal(),
            'totalPages'   => $draftModel->pager->getPageCount(),
        ]);
    }
    public function create()
    {
        try{
            $data=(object)$this->request->getVar();    
            $draftModel= new BorradoresModel();
            $draft=[
                'para'              =>$data->to,
                'asuntoNotificacion'=>$data->subject,
                'tipoFormato'       =>$data->formatType,
                'entidad'           =>$data->entity,
                'anioReporte'       =>$data->year,
                'trimestre'         =>$data->quarter,
                'plazo'             =>$data->deadline,
                'hora'              =>$data->hour,
                'tipoRequerimiento' =>$data->requirementType,
                'categoria'         =>$data->category,
                'sector'            =>$data->sector,
                'idTipoNotificacion'=>$data->notificationType,
                'radicado'          =>$data->caseNumber,
                'campania'          =>$data->campaign??null,
                'textoNotificacion' =>$data->notificationText,
                'fechaCreacion'     =>date('Y-m-d H:i:s'),     
            ];
            $$draftModel->save($draft);
            return $this->getResponse([
                'message' => 'Borrador guardado correctamente',
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
            $draftModel= new BorradoresModel();
            $updateDraft=[
                'para'              =>$vars->to,
                'asuntoNotificacion'=>$vars->subject,
                'tipoFormato'       =>$vars->formatType,
                'entidad'           =>$vars->entity,
                'anioReporte'       =>$vars->year,
                'trimestre'         =>$vars->quarter,
                'plazo'             =>$vars->deadline,
                'hora'              =>$vars->hour,
                'tipoRequerimiento' =>$vars->requirementType,
                'categoria'         =>$vars->category,
                'sector'            =>$vars->sector,
                'idTipoNotificacion'=>$vars->notificationType,
                'radicado'          =>$vars->caseNumber,
                'campania'          =>$vars->campaign??null,
                'textoNotificacion' =>$vars->notificationText,
                'fechaModificacion' =>date('Y-m-d H:i:s'),     
            ];
            $draftModel->where('id',$id)
                       ->set($updateDraft)
                       ->update();
            
            return $this->getResponse([
                'message' => 'Borrador actualizado correctamente',
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
            $draftModel= new BorradoresModel();
            $deleteDraft=$draftModel->find($id);
            if(empty($deleteDraft))throw new Exception('No se encontro el borrador',1);
            $draftModel->delete($id);
            return $this->getResponse([
                'message' => 'Borrador eliminado correctamente',
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
