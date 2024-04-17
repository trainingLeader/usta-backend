<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Exception;
use ReflectionException;
use App\Models\{FormatosModel,BlockchainModel};
use App\Libraries\SupportLogHandler;
class NotificationsExchangeController extends BaseController 
{
    use ResponseTrait;
    public function index()
    {
        
    }
    public function list()
    {
        try{
            $this->log->info('Listando notificaciones Blockchain');
            $vars=(Object)$this->request->getVar();
            $perPage = $vars->perPage??25;
            $page    = $vars->page??1;
            $filter  = [];
            if(isset($vars->search) and !empty($vars->search)){
                $filter['nombreFormato'] = $vars->search;
            }
            $filter  = [];
            if(isset($vars->caseNumber) and $vars->caseNumber!=""){
                $filter['radicado'] = $vars->caseNumber;
            }
            if(isset($vars->entity) and $vars->entity!=""){
                $filter['entidad'] = $vars->entity;
            }
            if(isset($vars->formatType) and $vars->formatType!=""){
                $filter['idFormato'] = $vars->formatType;
            }
            if(isset($vars->requirementType) and $vars->requirementType!=""){
                $filter['idRequerimiento'] = $vars->requirementType;
            }
            if(isset($vars->category) and $vars->category!=""){
                $filter['idCategoria'] = $vars->category;
            }
            if(isset($vars->sector) and $vars->sector!=""){
                $filter['sector'] = $vars->sector;
            }
            /* if(isset($vars->idStatus) and $vars->idStatus!=""){
                $filter['idEstadoNotificacion'] = $vars->idStatus;
            } */
            if(isset($vars->year) and $vars->year!=""){
                $filter['anioReporte'] = $vars->year;
            }
            if(isset($vars->quarter) and $vars->quarter!=""){
                $filter['trimestre'] = $vars->quarter;
            }
            if(isset($vars->deadline) and $vars->deadline!=""){
                $filter['plazo'] = $vars->deadline;
            }
            if(isset($vars->hour) and $vars->hour!=""){
                $filter['hora'] = $vars->hour;
            }
            if(isset($vars->idStatus)){
                $vars->idStatus=(int)$vars->idStatus;
            }
            $blockchainModel= new BlockchainModel();
            $blockchainModel->select('radicados.radicado as caseNumber,
                                      idNotificacion as idNotification,
                                      blockchain.id,
                                      blockchain.hashGenerado as firtsHash,
                                      zilliqa as firtsHashZilliqa,
                                      blockchain.hashGenerado2 as secondHash,
                                      zilliqa2 as secondHashZilliqa,
                                      blockchain.fechaCreacion as initDate,
                                      modulonotificaciones.entidad as entity,
                                      hilorespuestanotificacion.posicion as position,
                                      IF(hilorespuestanotificacion.estado=1,"Respondida","Sin respuesta") as status,
                                      IF(hilorespuestanotificacion.estado=0,DATEDIFF(NOW(),hilorespuestanotificacion.fechaCreacion),"--") as days');
            $blockchainModel->join('hilorespuestanotificacion','hilorespuestanotificacion.id=blockchain.idHiloRespuesta')
                            ->join('modulonotificaciones','modulonotificaciones.id=hilorespuestanotificacion.idNotificacion')
                            ->join('radicados','radicados.id=modulonotificaciones.idRadicado');
            if(isset($vars->email) and !empty($vars->email)){
                $blockchainModel->groupStart()
                                ->where('hilorespuestanotificacion.para',$vars->email)
                                ->orWhere('hilorespuestanotificacion.de',$vars->email)
                                ->groupEnd();
            }
            if(isset($vars->idStatus) and $vars->idStatus!="" and $vars->idStatus==1){
                $blockchainModel->where('hilorespuestanotificacion.estado',1);
            }
            if(isset($vars->idStatus) and $vars->idStatus!="" and $vars->idStatus==2){
                $blockchainModel->where('hilorespuestanotificacion.estado',0);
            }
            if(!empty($filter))$blockchainModel->where($filter);
            if(isset($vars->range))
            {
                $vars->range=(Object)$vars->range;
            }
            if(isset($vars->range->startDate) and $vars->range->startDate!="" and isset($vars->range->endDate) and $vars->range->endDate!=""){
                //usar between
                $blockchainModel->where('modulonotificaciones.plazo >=',$vars->range->startDate);
                $blockchainModel->where('modulonotificaciones.plazo <=',$vars->range->endDate.' 23:59:59');
            }
            $blockchainModel->orderBy('modulonotificaciones.idRadicado','ASC');
            $historyBlockchain=$blockchainModel->asObject()
                                               ->paginate(intval($perPage), 'default', intVal($page));
            
            $this->log->info('Listado de notificaciones Blockchain');
            return $this->getResponse([
                'message'      => 'OK',
                'items'        => $historyBlockchain,
                'totalRecords' => $blockchainModel->pager->getTotal(),
                'totalPages'   => $blockchainModel->pager->getPageCount(),
            ]);
        }
        catch(Exception $e){
            return $this->getResponse([
                'message' => $e->getMessage(),
                'error'   => true
            ], 500);
        }
    }
    public function getExcel()
    {
        try{
            $this->log->info('Exportando notificaciones Blockchain');
            $vars=(Object)$this->request->getVar();
            $filtersearch = new \stdClass();
            $range=new \stdClass();
            if(isset($vars->filterSearch)){
                $filtersearch = (Object)$vars->filterSearch;
                if(isset($filtersearch->range)){
                    $range = (Object)$filtersearch->range;
                }
            }
            $perPage = $vars->perPage??25;
            $page    = $vars->page??1;
            $filter  = [];

            if(isset($vars->search) and !empty($vars->search)){
                $filter['nombreFormato'] = $vars->search;
            }
            if(isset($vars->search) and !empty($vars->search)){
                $filter['nombreFormato'] = $vars->search;
            }
            $filter  = [];
            if(isset($filtersearch->caseNumber) and $filtersearch->caseNumber!=""){
                $filter['radicado'] = $filtersearch->caseNumber;
            }
            if(isset($filtersearch->entity) and $filtersearch->entity!=""){
                $filter['entidad'] = $filtersearch->entity;
            }
            if(isset($filtersearch->formatType) and $filtersearch->formatType!=""){
                $filter['idFormato'] = $filtersearch->formatType;
            }
            if(isset($filtersearch->requirementType) and $filtersearch->requirementType!=""){
                $filter['idRequerimiento'] = $filtersearch->requirementType;
            }
            if(isset($filtersearch->category) and $filtersearch->category!=""){
                $filter['idCategoria'] = $filtersearch->category;
            }
            if(isset($filtersearch->sector) and $filtersearch->sector!=""){
                $filter['sector'] = $filtersearch->sector;
            }
            if(isset($filtersearch->idStatus) and $filtersearch->idStatus!=""){
                $filter['idEstadoNotificacion'] = $filtersearch->idStatus;
            }
            if(isset($filtersearch->year) and $filtersearch->year!=""){
                $filter['anioReporte'] = $filtersearch->year;
            }
            if(isset($filtersearch->quarter) and $filtersearch->quarter!=""){
                $filter['trimestre'] = $filtersearch->quarter;
            }
            if(isset($filtersearch->deadline) and $filtersearch->deadline!=""){
                $filter['plazo'] = $filtersearch->deadline;
            }
            if(isset($filtersearch->hour) and $filtersearch->hour!=""){
                $filter['hora'] = $filtersearch->hour;
            }
            if(isset($filtersearch->hour) and $filtersearch->hour!=""){
                $filter['hora'] = $filtersearch->hour;
            }
            
            $blockchainModel= new BlockchainModel();
            
            $sql= $blockchainModel->getSLQForExcel($perPage, $page,$vars,$range, $filter);
            $path=WRITEPATH."excel";
            $pathSQL=$path. DIRECTORY_SEPARATOR.'sql'. DIRECTORY_SEPARATOR;
            $pathExcel=$path. DIRECTORY_SEPARATOR.'csv'. DIRECTORY_SEPARATOR;
            if(!file_exists($pathSQL)){
                mkdir($pathSQL, 0777, true);
            }
            if(!file_exists($pathExcel)){
                mkdir($pathExcel, 0777, true);
            }
            $generateNumber=uniqid();
            $file = fopen($pathSQL.$generateNumber.'.sql', "w");
            fwrite($file, $sql);
            fclose($file);
            $date=date("YmdHis");
            $sqlPath=$pathSQL.$generateNumber.'.sql';
            $csvPath=$pathExcel.$generateNumber.'-'.$date.'.csv';
            $csv = shell_exec('sh '.WRITEPATH.'exportExcel.sh '."$sqlPath $csvPath");
            if (!file_exists($csvPath)) {
                return $this->failNotFound('Archivo no encontrado');
            }
            $file = file_get_contents($csvPath);
            $this->response
                 ->setContentType('application/csv')
                 ->setHeader('Content-Disposition', 'attachment; filename="'.$generateNumber.'-'.$date.'.csv"');
            unlink($csvPath);
            $this->log->info('Notificaciones Blockchain exportadas');
            return $this->respond($file, HTTP_OK);
        }
        catch(Exception $e){
            $this->log->error($e->getMessage());
            return $this->getResponse([
                'message' => $e->getMessage(),
                'error'   => true
            ], 500);

        }
    }
    public function saveHash($hash,$id)
    {
        try{
            $this->log->info('Guardando hash Blockchain');
            $vars=(Object)$this->request->getVar();
            $blockchainModel= new BlockchainModel();
            $this->log->info('callback blockchain: '.$vars->TranID);
            $blockchainModel->select('id');
            $blockchainModel->where('id',$id);
            $blockchain=$blockchainModel->first();
            if(!isset($vars->TranID) or $vars->TranID==""){
                return $this->failNotFound('Transacción no puede estar vacia');
            }
            if($blockchain==null){
                return $this->failNotFound('Notificación no encontrada');
            }
            if($hash==""){
                return $this->failNotFound('Hash no puede estar vacio');
            }
            if($hash=="hash1"){
                $blockchainModel->update($id,[
                    'zilliqa'      => $vars->TranID
                ]);
            }
            if($hash=="hash2"){
                $blockchainModel->update($id,[
                    'zilliqa2'      => $vars->TranID
                ]);
            }
            $this->log->info('Hash Blockchain guardado');
            return $this->getResponse([
                'message' => 'OK'
            ]);
        }
        catch(Exception $e){
            $this->log->error($e->getMessage());
            return $this->getResponse([
                'message' => $e->getMessage(),
                'error'   => true
            ], 500);
        }
    }
}
