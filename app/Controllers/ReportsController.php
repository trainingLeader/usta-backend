<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Database\Migrations\DraftsView;
use App\Models\BorradoresModel;
use App\Models\DraftsViewModel;
use CodeIgniter\API\ResponseTrait;
use Exception;
use ReflectionException;
use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\RequestInterface;
use TCPDF;
use App\Models\{CategoryModel,
    FormatosModel,
    TipoRequerimientoModel,
    RadicadosModel,
    ModuloNotificacionesModel,
    DestinatarioModel,
    HiloRespuestaNotificacionModel,
    BlockchainModel,
    CampaniaModel, 
    ReportsViewModel,
    AnioActualModel,
    RecordatoriosModel
};
use App\Libraries\SupportLogHandler;
use App\Libraries\SoapServices;
class ReportsController extends BaseController 
{
    use ResponseTrait;
    public function list()
    {
        try{
            $this->log->info('Entrando al listando de reportes');
            $vars=(Object)$this->request->getVar();
            $perPage = $vars->perPage??25;
            $page    = $vars->page??1;
            $filter  = [];
            if(isset($vars->typeNotification) and $vars->typeNotification!=""){
                $filter['typeNotification'] = $vars->typeNotification;
            }
            if(isset($vars->campain) and $vars->campain!=""){
                $filter['idCampain'] = $vars->campain;
            }
            if(isset($vars->year) and $vars->year!=""){
                $filter['year'] = $vars->year;
            }

            $notificationsModel = new ReportsViewModel();
            $reportsModel       = new ReportsViewModel();
            $totalAgentsModel   = new ReportsViewModel();
            $deadlineModel      = new ReportsViewModel();
            $readedModel        = new ReportsViewModel();
            $totaldeadlineExtended=$deadlineModel->select('
                                                    COUNT(distinct caseNumber) as total
                                                ');
            $totalReaded=$readedModel ->select('
                                                    COUNT(distinct caseNumber) as total
                                                ');
            $statusNotification = $reportsModel->select('
                color,COUNT(*) as total,
                (CASE
                    WHEN color = "Verde" THEN "A tiempo"
                    WHEN color = "Amarillo" THEN "Próximo a vencer"
                    WHEN color = "Rojo" THEN "Vencido"
                    WHEN color = "Morado" THEN "Notificación cerrada"
                    ELSE ""
                END )AS statusNotification
            ');
            $notificationsModel->select('
            id,
            idCampain,
            IF(campain IS NULL,"(N/A)",campain) as campain,
            `from`,
            `to`,
            (CASE
                WHEN color = "Verde" THEN "A tiempo"
                WHEN color = "Amarillo" THEN "Próximo a vencer"
                WHEN color = "Rojo" THEN "Vencido"
                WHEN color = "Morado" THEN "Notificación cerrada"
                ELSE ""
            END )AS statusNotification,
            caseNumber,
            deadline,
            observations,
            COALESCE((
                SELECT resp.dateResponse
                FROM reportsview resp
                WHERE resp.caseNumber = reportsview.caseNumber
                  AND resp.`from` = reportsview.`to`
                  AND resp.`to` = reportsview.`from`
                  ORDER BY  id ASC
                LIMIT 1
            ), "(N/A)") AS dateResponse
            ');
            $this->setFiltersQuery($vars,$filter,$notificationsModel);
            $this->setFiltersQuery($vars,$filter,$totaldeadlineExtended);
            $this->setFiltersQuery($vars,$filter,$statusNotification);
            $this->setFiltersQuery($vars,$filter,$readedModel);
            $totaldeadlineExtended=$totaldeadlineExtended->asObject()
                                                       ->where('deadlineExtended',1)
                                                       ->first();
            $totalReaded=$readedModel->asObject()
                                    ->where('hash2 is not null')
                                    ->first();
            $notificationsModel->groupBy('caseNumber');
            $notificationsModel->orderBy('id','ASC');
            $notifications=$notificationsModel->asObject()->paginate(intval($perPage), 'default', intVal($page)); 
            $ultimateQuery=$notificationsModel->getLastQuery();
            $statusNotification=$statusNotification->groupBy('color')
                                                   ->asObject()
                                                   ->findAll();
            $range=new \stdClass();
            if(isset($vars->range)){
                $range = (Object)$vars->range;
            }
            $totalAgens=$totalAgentsModel->getTotalAgents($perPage, $page,$vars,$range, $filter);  
                                            
            $dataForChart=[];
            foreach($statusNotification as $item){
                if(!isset($vars->chart) or $vars->chart!="Torta")
                $dataForChart[]=[
                    'name' => $item->statusNotification,
                    'Agentes' => $item->total,
                ];
                if(isset($vars->chart) and $vars->chart=="Torta")
                $dataForChart[]=[
                    'name' => $item->statusNotification,
                    'value' => $item->total,
                ];
            }
            $this->log->info('Saliendo del listando de reportes');
            return $this->getResponse([
                'message'      => 'OK',
                'items'        => $notifications,
                'totalRecords' => $notificationsModel->pager->getTotal(),
                'totalPages'   => $notificationsModel->pager->getPageCount(),
                'dataForChart' => $dataForChart,
                'totalAgents'  => $totalAgens,
                'totaldeadlineExtended' => $totaldeadlineExtended->total,
                'totalReaded' => $totalReaded->total,
            ]);
        }catch(Exception $e){
            //ultima consulta ejecutada
            $sql=$notificationsModel->getLastQuery();
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError($message);
        }
    }
    public function setFiltersQuery(&$vars,&$filter,ReportsViewModel &$model){
        if(!empty($filter)){
            $model->where($filter);
        }
        if(isset($vars->search) and !empty($vars->search)){
            $model->groupStart();
            $model->like('subject',$vars->search);
            $model->orLike('body' ,$vars->search);
            $model->groupEnd();
        }
        if(isset($vars->email) and !empty($vars->email)){
            $model->where('to',$vars->email);
        }
        if(isset($vars->status) and !empty($vars->status) and !is_array($vars->status)){
            $model->where('status',$vars->status);
        }
        if(isset($vars->status) and !empty($vars->status) and is_array($vars->status)){
            $model->whereIn('status',$vars->status);
        }
        //rango de fechas
        if(isset($vars->range))
        {
            $vars->range=(Object)$vars->range;
        }
        if(isset($vars->range->startDate) and $vars->range->startDate!="" and isset($vars->range->endDate) and $vars->range->endDate!=""){
            //usar between
            $model->where('deadline >=',$vars->range->startDate);
            $model->where('deadline <=',$vars->range->endDate.' 23:59:59');
        }
        return $model;
    }
    public function getMassiveFile(string $file){
        $path=WRITEPATH . 'notifications/massive' . DIRECTORY_SEPARATOR.$file;
        if(file_exists($path)){
            return base64_encode(file_get_contents($path));
        }
        return null;
    }
    public function listMail()
    {
        try{
            $vars=(Object)$this->request->getVar();
            $perPage = $vars->perPage??25;
            $page    = $vars->page??1;
            $filter  = [];
            if(isset($vars->caseNumber) and $vars->caseNumber!=""){
                $filter['caseNumber'] = $vars->caseNumber;
            }
            if(isset($vars->entity) and $vars->entity!=""){
                $filter['entity'] = $vars->entity;
            }
            if(isset($vars->formatType) and $vars->formatType!=""){
                $filter['formatType'] = $vars->formatType;
            }
            if(isset($vars->requirementType) and $vars->requirementType!=""){
                $filter['requirementType'] = $vars->requirementType;
            }
            if(isset($vars->category) and $vars->category!=""){
                $filter['category'] = $vars->category;
            }
            if(isset($vars->sector) and $vars->sector!=""){
                $filter['sector'] = $vars->sector;
            }
            if(isset($vars->idStatus) and $vars->idStatus!=""){
                $filter['idStatus'] = $vars->idStatus;
            }
            if(isset($vars->year) and $vars->year!=""){
                $filter['year'] = $vars->year;
            }
            if(isset($vars->quarter) and $vars->quarter!=""){
                $filter['quarter'] = $vars->quarter;
            }
            if(isset($vars->deadline) and $vars->deadline!=""){
                $filter['deadline'] = $vars->deadline;
            }
            if(isset($vars->hour) and $vars->hour!=""){
                $filter['hour'] = $vars->hour;
            }

            $notificationsModel= new ReportsViewModel();
            $notificationsModel->select('
                id,
                caseNumber,
                IF(sendDate IS NULL,"---",sendDate) as sendDate,
                IF(reminderSent IS NULL,0,reminderSent) as reminderSent,
            ');
            if(!empty($filter))$notificationsModel->where($filter);
            if(isset($vars->search) and !empty($vars->search)){
                $notificationsModel->groupStart();
                $notificationsModel->like('subject',$vars->search);
                $notificationsModel->orLike('body' ,$vars->search);
                $notificationsModel->groupEnd();
            }
            if(isset($vars->email) and !empty($vars->email)){
                $notificationsModel->where('to',$vars->email);
            }
            if(isset($vars->status) and !empty($vars->status) and !is_array($vars->status)){
                $notificationsModel->where('status',$vars->status);
            }
            if(isset($vars->status) and !empty($vars->status) and is_array($vars->status)){
                $notificationsModel->whereIn('status',$vars->status);
            }
            //rango de fechas
            if(isset($vars->range))
            {
                $vars->range=(Object)$vars->range;
            }
            if(isset($vars->range->startDate) and $vars->range->startDate!="" and isset($vars->range->endDate) and $vars->range->endDate!=""){
                //usar between
                $notificationsModel->where('sendDate >=',$vars->range->startDate);
                $notificationsModel->where('sendDate <=',$vars->range->endDate.' 23:59:59');
            }
            $notificationsModel->orderBy('id','ASC');
            $notificationsModel->groupBy('caseNumber');
            $notifications=$notificationsModel->asObject()->paginate(intval($perPage), 'default', intVal($page));
            $totalCurrentMonth=$notificationsModel->asObject()
                                                ->select('COUNT(*) as totalCurrentMonth')
                                                ->where('MONTH(sendDate)',date('m'))
                                                ->first();
            $totalPreviousMonth=$notificationsModel->asObject()
                                                ->select('COUNT(*) as totalPreviousMonth')
                                                ->where('MONTH(sendDate)',date('m',strtotime('-1 month')))
                                                ->first();
            return $this->getResponse([
                'message'      => 'OK',
                'items'        => $notifications,
                'totalRecords' => $notificationsModel->pager->getTotal(),
                'totalPages'   => $notificationsModel->pager->getPageCount(),
                'totalCurrentMonth' => $totalCurrentMonth->totalCurrentMonth,
                'totalPreviousMonth' => $totalPreviousMonth->totalPreviousMonth,
            ]);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError($message);
        }
    }
    public function getEntitys()
    {
        $params=(Object)$this->request->getVar();
        try{
            //si la peticion es cancelada
            if($this->request->getMethod()=='OPTIONS'){
                return $this->getResponse([
                    'items'   => [],
                ]);
            }
            if(!isset($params->entity) or $params->entity==""){
                return $this->getResponse([
                    'items'   => [],
                ]);
            }
            $entityModel= new ReportsViewModel();
            $entitys=$entityModel->select('id,entity as name')
                                ->where('entity IS NOT NULL')
                                ->like('entity',$params->entity)
                                ->groupBy('entity')
                                ->orderBy('entity','ASC')
                                ->asObject()
                                ->findAll();
            return $this->getResponse([
                'items'   => $entitys,
            ]);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function getExcelForReportsMails()
    {
        try{
            $this->log->info('Entrando a la generación de excel para reportes de correos');
            $vars=(Object)$this->request->getVar();
            $perPage = $vars->perPage??25;
            $page    = $vars->page??1;
            $filter  = [];
            $range=new \stdClass();
            $filtersearch =new \stdClass();
            if(isset($vars->filterSearch)){
                $filtersearch = (Object)$vars->filterSearch;
                if(isset($filtersearch->range)){
                    $range = (Object)$filtersearch->range;
                }
            }
            if(isset($filtersearch->caseNumber) and $filtersearch->caseNumber!=""){
                $filter['caseNumber'] = $filtersearch->caseNumber;
            }
            if(isset($filtersearch->entity) and $filtersearch->entity!=""){
                $filter['entity'] = $filtersearch->entity;
            }
            if(isset($filtersearch->formatType) and $filtersearch->formatType!=""){
                $filter['formatType'] = $filtersearch->formatType;
            }
            if(isset($filtersearch->requirementType) and $filtersearch->requirementType!=""){
                $filter['requirementType'] = $filtersearch->requirementType;
            }
            if(isset($filtersearch->category) and $filtersearch->category!=""){
                $filter['category'] = $filtersearch->category;
            }
            if(isset($filtersearch->sector) and $filtersearch->sector!=""){
                $filter['sector'] = $filtersearch->sector;
            }
            if(isset($filtersearch->idStatus) and $filtersearch->idStatus!=""){
                $filter['idStatus'] = $filtersearch->idStatus;
            }
            if(isset($filtersearch->year) and $filtersearch->year!=""){
                $filter['year'] = $filtersearch->year;
            }
            if(isset($filtersearch->quarter) and $filtersearch->quarter!=""){
                $filter['quarter'] = $filtersearch->quarter;
            }
            if(isset($filtersearch->deadline) and $filtersearch->deadline!=""){
                $filter['deadline'] = $filtersearch->deadline;
            }
            if(isset($filtersearch->hour) and $filtersearch->hour!=""){
                $filter['hour'] = $filtersearch->hour;
            }

            $notificationsModel= new ReportsViewModel();
            $sql= $notificationsModel->getSLQForExcel($perPage, $page,$vars,$range, $filter);
            $this->log->info('Sql generado para el excel: '.$sql);
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
            $file = fopen($pathSQL.'massiveMail'.$generateNumber.'.sql', "w");
            fwrite($file, $sql);
            fclose($file);
            $date=date("YmdHis");
            $sqlPath=$pathSQL.'massiveMail'.$generateNumber.'.sql';
            $csvPath=$pathExcel.'massiveMail'.$generateNumber.'-'.$date.'.csv';
            $csv = shell_exec('sh '.WRITEPATH.'exportExcel.sh '."$sqlPath $csvPath");
            if (!file_exists($csvPath)) {
                return $this->failNotFound('Archivo no encontrado');
            }
            $file = file_get_contents($csvPath);
            $encode=mb_convert_encoding($file, 'UTF-16LE', 'UTF-8');
            $this->response
                 ->setContentType('application/csv')
                 ->setHeader('Content-Disposition', 'attachment; filename="'.$generateNumber.'-'.$date.'.csv"');
                 
            unlink($csvPath);
            return $this->respond($encode, HTTP_OK);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError($message);
        }
    }
    public function getExcelForReports()
    {
        try{
            $vars=(Object)$this->request->getVar();
            $perPage = $vars->perPage??25;
            $page    = $vars->page??1;
            $filter  = [];
            $range=new \stdClass();
            $filtersearch=new \stdClass();
            if(isset($vars->filterSearch)){
                $filtersearch = (Object)$vars->filterSearch;
                if(isset($filtersearch->range)){
                    $range =(Object)$filtersearch->range;
                }
            }
            if(isset($filtersearch->typeNotification) and $filtersearch->typeNotification!=""){
                $filter['typeNotification'] = $filtersearch->typeNotification;
            }
            if(isset($filtersearch->campain) and $filtersearch->campain!=""){
                $filter['idCampain'] = $filtersearch->campain;
            }
            if(isset($filtersearch->year) and $filtersearch->year!=""){
                $filter['year'] = $filtersearch->year;
            }
            $notificationsModel= new ReportsViewModel();
            $sql= $notificationsModel->getSLQForExcelReports($perPage, $page,$vars,$range, $filter);
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
            $file = fopen($pathSQL.'reports'.$generateNumber.'.sql', "w");
            fwrite($file, $sql);
            fclose($file);
            $date=date("YmdHis");
            $sqlPath=$pathSQL.'reports'.$generateNumber.'.sql';
            $csvPath=$pathExcel.'reports'.$generateNumber.'-'.$date.'.csv';
            $csv = shell_exec('sh '.WRITEPATH.'exportExcel.sh '."$sqlPath $csvPath");
            if (!file_exists($csvPath)) {
                return $this->failNotFound('Archivo no encontrado');
            }
            $file = file_get_contents($csvPath);
            $this->response
                 ->setContentType('application/csv')
                 ->setHeader('Content-Disposition', 'attachment; filename="'.$generateNumber.'-'.$date.'.csv"');
            unlink($csvPath);
            return $this->respond($file, HTTP_OK);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError($message);
        }
    }
    public function getCampains()
    {
        $params=(Object)$this->request->getVar();
        try{
            //si la peticion es cancelada
            if($this->request->getMethod()=='OPTIONS'){
                return $this->getResponse([
                    'items'   => [],
                ]);
            }
            if(!isset($params->campain) or $params->campain==""){
                return $this->getResponse([
                    'items'   => [],
                ]);
            }
            $campainModel= new CampaniaModel();
            $campains=$campainModel->select('campania.id,campania.nombre as name')
                                ->join('reportsview','reportsview.idCampain=campania.id')
                                ->like('nombre',$params->campain)
                                ->groupBy('campania.nombre')
                                ->orderBy('nombre','ASC')
                                ->asObject()
                                ->findAll();
            return $this->getResponse([
                'items'   => $campains,
            ]);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
}
