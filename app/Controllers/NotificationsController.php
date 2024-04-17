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
    NotificationViewModel,
    AnioActualModel,
    RecordatoriosModel,
    HistorialCambiosModel,
};
use App\Libraries\SupportLogHandler;
use App\Libraries\SoapServices;
class NotificationsController extends BaseController 
{
    use ResponseTrait;
    public function index()
    {
        
    }
    public function list()
    {
        try{
            $this->log->info('Entrando al listando notificaciones');
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
            if(isset($vars->idStatus) and $vars->idStatus!="" and !(isset($vars->rol) and $vars->rol=="Agente")){
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

            $notificationsModel= new NotificationViewModel();
            $notificationsModel->select('*');
            //$notificationsModel->where('idThread = (SELECT MIN(t2.idThread) FROM notificationview t2 WHERE t2.id = notificationview.id)');
            if(!empty($filter))$notificationsModel->where($filter);
            if(isset($vars->search) and !empty($vars->search)){
                $notificationsModel->groupStart();
                $notificationsModel->like('subject',$vars->search);
                $notificationsModel->orLike('messageNotification' ,$vars->search);
                $notificationsModel->groupEnd();
            }
            if(isset($vars->email) and !empty($vars->email)){
                $notificationsModel->where('to',$vars->email);
            }
            if(isset($vars->rol) and $vars->rol=="Agente" and $vars->status!="Archivadas"){
                $notificationsModel->where('idStatusAgente',null);
            }
            if(isset($vars->rol) and $vars->rol=="Agente" and $vars->status=="Archivadas"){
                $notificationsModel->where('idStatusAgente',3);
            }
            
            if(isset($vars->status) and !empty($vars->status) and !is_array($vars->status)){
                if(!(isset($vars->rol) and $vars->rol=="Agente")){
                    $notificationsModel->where('status',$vars->status);
                }
            }
            if(isset($vars->status) and !empty($vars->status) and is_array($vars->status)){
                if(!(isset($vars->rol) and $vars->rol=="Agente")){
                    $notificationsModel->whereIn('status',$vars->status);
                }
            }
            if(isset($vars->idStatus) and $vars->idStatus!="" and isset($vars->rol) and $vars->rol=="Agente"){
                $notificationsModel->whereIn('idStatus',[3,5]);
            }
            //rango de fechas
            if(isset($vars->range))
            {
                $vars->range=(Object)$vars->range;
            }
            if(isset($vars->range->startDate) and $vars->range->startDate!="" and isset($vars->range->endDate) and $vars->range->endDate!=""){
                //usar between
                $notificationsModel->where('deadline >=',$vars->range->startDate);
                $notificationsModel->where('deadline <=',$vars->range->endDate.' 23:59:59');
            }
            $notificationsModel->groupBy('caseNumber');
            $notificationsModel->orderBy('id','ASC');
            $notifications=$notificationsModel->asObject()->paginate(intval($perPage), 'default', intVal($page));        
            $threadModel= new HiloRespuestaNotificacionModel();
            $changesModel= new HistorialCambiosModel();
            foreach($notifications as $notification){
                $threads=$threadModel->select('de as from,
                                               para as to,
                                               mensaje as body,
                                               hilorespuestanotificacion.fechaCreacion as date,
                                               blockchain.zilliqa,
                                               blockchain.id as idBlockchain,
                                               hilorespuestanotificacion.adjunto as attachedFile,
                                               IF(blockchain.hashGenerado2 is NULL,"withoutSee","readed") as readed
                                               ')
                                    ->where('idNotificacion',$notification->id)
                                    ->join('blockchain','blockchain.idHiloRespuesta=hilorespuestanotificacion.id')
                                    ->orderBy('hilorespuestanotificacion.id','ASC')
                                    ->asObject()
                                    ->findAll();
                $first=$threads[0];
                $replys=[];
                foreach($threads as $thread){
                    $replys[]=[
                        'body' => $thread->body,
                        'from' => (object)[
                            'name' => 'usta',
                            'email'=> $thread->from,
                        ],
                        'to'   => (object)[
                            'name' => 'usuario',
                            'email'=> $thread->to,
                        ],
                        'date' => $thread->date,
                        'readed' => $thread->readed,
                        'idBlockchain' => $thread->idBlockchain,
                        'attachedFile' => $thread->attachedFile,
                    ];
                }
                $notification->content=(object)[
                    'body' => $notification->body,
                    'from' => (object)[
                        'name' => 'usta',
                        'email'=> $first->from,
                    ],
                    'to'   => (object)[
                        'name' => 'usuario',
                        'email'=> $first->to,
                    ],
                    'replys'=> $replys,
                ];
                $notification->name='usta';
                $notification->hash=$first->zilliqa;
                $changes=$changesModel->asObject()->select('email,fechaCreacion,tipo')->where('idNotificacion',$notification->id)->findAll();
                $notification->changes=[
                    'create' => null,
                    'update' => [],
                    'send'   => null,
                ];
                foreach($changes as $change){
                    if($change->tipo=='create'){
                        $notification->changes['create']=['date'=>$change->fechaCreacion,'email'=>$change->email];
                    }
                    if($change->tipo=='update'){
                        $notification->changes['update'][]=['date'=>$change->fechaCreacion,'email'=>$change->email];
                    }
                    if($change->tipo=='send'){
                        $notification->changes['send']=['date'=>$change->fechaCreacion,'email'=>$change->email];
                    }
                }
                unset($notification->body);
            }
            $this->log->info('Listado de notificaciones cargado correctamente');
            return $this->getResponse([
                'message'      => 'OK',
                'items'        => $notifications,
                'totalRecords' => $notificationsModel->pager->getTotal(),
                'totalPages'   => $notificationsModel->pager->getPageCount(),
            ]);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError($message);
        }
    }
    public function ListDrafts()
    {
        try
        {
            $this->log->info('Entrando al listado de borradores');
            $vars    = (Object)$this->request->getVar();
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
            $draftsModel= new DraftsViewModel();
            $draftsModel->select('*');
            if(!empty($filter))$draftsModel->where($filter);
            if(isset($vars->search) and !empty($vars->search)){
                $draftsModel->groupStart();
                $draftsModel->like('subject',$vars->search);
                $draftsModel->orLike('body' ,$vars->search);
                $draftsModel->groupEnd();
            }
            if(isset($vars->email) and !empty($vars->email)){
                $draftsModel->where('to',$vars->email);
            }
            if(isset($vars->status) and !empty($vars->status) and !is_array($vars->status)){
                $draftsModel->where('status',$vars->status);
            }
            if(isset($vars->status) and !empty($vars->status) and is_array($vars->status)){
                $draftsModel->whereIn('status',$vars->status);
            }
            //rango de fechas
            if(isset($vars->range))
            {
                $vars->range=(Object)$vars->range;
            }
            if(isset($vars->range->startDate) and $vars->range->startDate!="" and isset($vars->range->endDate) and $vars->range->endDate!=""){
                //usar between
                $draftsModel->where('deadline >=',$vars->range->startDate);
                $draftsModel->where('deadline <=',$vars->range->endDate.' 23:59:59');
            }
            $draftsModel->orderBy('id','ASC');
            $drafts=$draftsModel->asObject()->paginate(intval($perPage), 'default', intVal($page));
            //cargar los archivos que tienen el campo archivoMasivo diferente de null y cargarlos items
            foreach($drafts as $draft){
                if($draft->archivoMasivo!=null){
                    $draft->massiveFile=$this->getMassiveFile($draft->archivoMasivo);
                }
            }  
            $this->log->info('Listado de borradores cargado correctamente');          
            return $this->getResponse([
                'message'      => 'OK',
                'items'        => $drafts,
                'totalRecords' => $draftsModel->pager->getTotal(),
                'totalPages'   => $draftsModel->pager->getPageCount(),
            ]);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError($message);
        }
    }
    public function getMassiveFile(string $file){
        $path=WRITEPATH . 'notifications/massive' . DIRECTORY_SEPARATOR.$file;
        if(file_exists($path)){
            return base64_encode(file_get_contents($path));
        }
        return null;
    }
    public function cleanData(object $data){
        $data->from=$data->from??'usta@usta.com';
        if($data->caseNumber<=0 or empty($data->caseNumber)){
            throw new Exception('El numero de radicado es requerido',1);
        }
        if($data->to==""){
            throw new Exception('El destinatario es requerido',1);
        }
        if($data->subject==""){
            throw new Exception('El asunto es requerido',1);
        }
        $toModel= new DestinatarioModel();
        $existTo=$toModel->where('email',$data->to)->first();
        if(empty($existTo)){
            $toModel->save([
                'email' => $data->to,
            ]);
            $to=$toModel->insertID();
        }
        if($data->formatType<=0 or $data->formatType==""){
            $data->formatType=null;
        }
        if($data->body=="" or $data->body=="<p><br></p>"){
            throw new Exception('El cuerpo del mensaje es requerido',1);
        }
        if($data->entity==""){
            $data->entity=null;
        }
        if($data->year==""){
            $data->year=null;
        }
        if($data->quarter<=0 or $data->quarter==""){
            $data->quarter=null;
        }
        if($data->deadline=="" or $data->deadline=="null"){
            $data->deadline=null;
        }
        if($data->hour==""){
            $data->hour=null;
        }
        if($data->requirementType<=0 or $data->requirementType==""){
            $data->requirementType=null;
        }
        if($data->category=="" ){
            $data->category=null;
        }
        if($data->sector<=0 or $data->sector==""){
            $data->sector=null;
        }
        return $data;
    }
    public function cleanMassiveData(&$data){
        
        if($data->body=="" or $data->body=="<p><br></p>"){
            throw new Exception('El cuerpo del mensaje es requerido',1);
        }
        if($data->campainName==""){
            throw new Exception('El nombre de la campaña es requerido',1);
        }
        if($data->formatType<=0 or $data->formatType==""){
            throw new Exception('El tipo de formato es requerido',1);
        }
        if($data->year==""){
            throw new Exception('El año de reporte es requerido',1);
        }
        if($data->quarter<=0 or $data->quarter==""){
            throw new Exception('El trimestre es requerido',1);
        }
        if($data->deadline=="" or $data->deadline=="null"){
            throw new Exception('El plazo es requerido',1);
        }
        if($data->hour==""){
            throw new Exception('La hora es requerida',1);
        }
        if($data->requirementType<=0 or $data->requirementType==""){
            throw new Exception('El tipo de requerimiento es requerido',1);
        }
        if($data->category==""){
            throw new Exception('La categoria es requerida',1);
        }
    }
    public function saveNotification(){
        try{
            $this->log->info('Entrando al guardado de notificaciones');
            $reminderModel= new RecordatoriosModel();
            $reminder=$reminderModel->find(1);
            if(!$reminder){
                throw new Exception('No se ha configurado los recordatorios, por favor comuniquese con la persona responsable de configuración',1);
            }
            $data=(object)$this->request->getVar(); 
            $data=$this->cleanData($data);
            $caseNumber=$this->getCaseNumber($data->caseNumber);
            $data->caseNumber=$caseNumber->id;
            $data->file=$this->saveAttachedFile($caseNumber->radicado);
            $notificationModel= new BorradoresModel();
            $exist=(object)$notificationModel->where('idRadicado',$data->caseNumber)->first();
            $createDate=date('Y-m-d H:i:s');
            $saveData=[
                'asuntoNotificacion' => $data->subject,
                'idTipoNotificacion' => 5,
                'idRadicado'         => $data->caseNumber,
                'idEstadoNotificacion'=> 4,
                'idFormato'          => $data->formatType,
                'idRequerimiento'    => $data->requirementType,
                'idCategoria'        => $data->category,
                'idCampania'         => null,
                'anioReporte'        => $data->year,
                'entidad'            => $data->entity,
                'trimestre'          => $data->quarter,
                'plazo'              => $data->deadline,
                'hora'               => $data->hour,
                'sector'             => $data->sector,
                'textoNotificacion'  => $data->body,
                'adjunto'            => $data->file,
                'fechaCreacion'      => $createDate,
                'para'               => $data->to,
                'de'                 => $data->from,
            ];
            $historialCambiosModel= new HistorialCambiosModel();
            if(isset($exist->id) and $exist->id>0){
                if($saveData['adjunto'] == null){
                    $saveData['adjunto'] = $exist->adjunto;
                }
                $notificationModel->where('id',$exist->id)->set($saveData)->update();
                $historialCambiosModel->save([
                    'idBorrador' => $exist->id,
                    'nombre'     => $data->name,
                    'tipo'       => 'update',
                    'email'      => $data->emailSave,
                    'fechaCreacion' => $createDate,
                ]);
            }
            else{
                $notificationModel->save($saveData);
                $idNotification=$notificationModel->insertID();
                $historialCambiosModel->save([
                    'idBorrador' => $idNotification,
                    'nombre'     => $data->name,
                    'tipo'       => 'create',
                    'email'      => $data->emailSave,
                    'fechaCreacion' => $createDate,
                ]);
            }
            $this->log->info('Notificacion guardada correctamente');
            return $this->getResponse([
                'message' => 'Notificacion guardada correctamente',
            ]);

        }catch(Exception $e){
            $message=$e->getMessage();
            if($e->getCode()==1){
                $this->log->info($message);
                return $this->getResponse(['message' => $message],HTTP_BAD_REQUEST);
            }
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function createUniqueNotification()
    {
        try{
            $this->log->info('Entrando a la creación de notificaciones');
            $reminderModel= new RecordatoriosModel();
            $reminder=$reminderModel->find(1);
            if(!$reminder){
                throw new Exception('No se ha configurado los recordatorios, por favor comuniquese con la persona responsable de configuración',1);
            }
            $data=(object)$this->request->getVar(); 
            $data=$this->cleanData($data);
            if($data->deadline==null){
                throw new Exception('El plazo es requerido',1);
            }
            if($data->hour==null){
                throw new Exception('La hora es requerida',1);
            }

            $caseNumber=$this->getCaseNumber($data->caseNumber);
            $data->caseNumber=$caseNumber->id;
            $data->file=$this->saveAttachedFile($caseNumber->radicado);
            if($data->file==null){
                $draftsModel= new BorradoresModel();
                $drafts=(object)$draftsModel->where('idRadicado',$data->caseNumber)->first();
                if(isset($drafts->adjunto) and $drafts->adjunto!=null){
                    $data->file=$drafts->adjunto;
                }
            }
            $notificationModel= new ModuloNotificacionesModel();
            $createDate=date('Y-m-d H:i:s');
            $notificationModel->save([
                'asuntoNotificacion' => $data->subject,
                'idTipoNotificacion' => 5,
                'idRadicado'         => $data->caseNumber,
                'idEstadoNotificacion'=> 2,
                'idFormato'          => $data->formatType,
                'idRequerimiento'    => $data->requirementType,
                'idCategoria'        => $data->category,
                'idCampania'         => null,
                'anioReporte'        => $data->year,
                'entidad'            => $data->entity,
                'trimestre'          => $data->quarter,
                'plazo'              => $data->deadline,
                'hora'               => $data->hour,
                'sector'             => $data->sector,
                'textoNotificacion'  => $data->body,
                'adjunto'            => $data->file,
                'fechaCreacion'      => $createDate,
            ]);
            $notification=$notificationModel->insertID();
            $threadModel= new HiloRespuestaNotificacionModel();
            $threadModel->save([
                'idNotificacion' => $notification,
                'de'             => $data->from,
                'para'           => $data->to,
                'mensaje'        => $data->body,
                'adjunto'        => $data->file,
                'fechaCreacion'  => $createDate,
            ]);
            $idThread=$threadModel->insertID();
            $hash=hash('sha256',$notification.$data->caseNumber.$data->from.$data->to.$data->subject.$data->body);
            $blockchainModel= new BlockchainModel();
            
            $blockchainModel->save([
                'idTipoNotificacion' => 5,
                'idHiloRespuesta'    => $threadModel->insertID(),
                'idAuditoria'        => null,
                'hashGenerado'       => $hash,
                'zilliqa'            => null,
                'hashGenerado2'      => null,
                'zilliqa2'           => null,
                'fechaCreacion'      => $createDate,
            ]);
            $idBlockchain=$blockchainModel->insertID();
            $data->date=$createDate;
            $data->hourCreate=date('h:i:s A');
            $data->caseNumber=$caseNumber->radicado;
            $statusOnbase=$this->saveDocument($data);
            if($statusOnbase->Code=='00'){
                $threadModel->where('id',$idThread)
                            ->set(['estadoOnbase'=>1,'DocumentHandleOnbase'=>$statusOnbase->DocumentHandle])
                            ->update();
            }
            $draftsModel= new BorradoresModel();
            $idDraft=(object)$draftsModel->where('idRadicado',$caseNumber->id)->first();
            $historialCambiosModel= new HistorialCambiosModel();
            $historialCambiosModel->save([
                'idNotificacion' => $notification,
                'nombre'         => $data->name,
                'tipo'           => 'send',
                'email'          => $data->emailSave,
                'fechaCreacion'  => $createDate,
            ]);
            if(isset($idDraft->id) and $idDraft->id>0){
               $isUpdate= $historialCambiosModel->where('idBorrador',$idDraft->id)
                                      ->set(['idNotificacion'=>$notification])
                                      ->update();
            }
            $draftsModel->where('idRadicado',$caseNumber->id)->delete();
            $zilliqa=$this->hashBlockchain($hash,"hash1",$idBlockchain);
            $blockchainModel->where('id',$idBlockchain)->set(['zilliqa'=>$zilliqa])->update();
            $this->log->info('Notificacion creada correctamente');
            return $this->getResponse([
                'message' => 'Notificacion creada correctamente',
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
    public function saveMassiveNotification()
    {
        try{
            $this->log->info('Entrando al listando notificaciones');
            $reminderModel= new RecordatoriosModel();
            $reminder=$reminderModel->find(1);
            if(!$reminder){
                throw new Exception('No se ha configurado los recordatorios, por favor comuniquese con la persona responsable de configuración',1);
            }
            $data=(object)$this->request->getVar();
            $data->from=$data->from??'usta@usta.com';
            //$this->cleanMassiveData($data);
            $file=$this->request->getFile('file');
            $campainModel= new CampaniaModel();
            $draftsModel= new BorradoresModel();
            $exist=null;
            if(isset($data->id) and $data->id>0){
                $exist=(object)$draftsModel->where('id',$data->id)->first();
            }
            $existCampain=$campainModel->asObject()->where('nombre',$data->campainName)->first();
            $massiveFile=null;
            if(isset($exist->archivoMasivo) and $exist->archivoMasivo!=null){
                $massiveFile=$exist->archivoMasivo;
            }
            if($file !=null and $file->isValid()){ 
                $massiveFile=$this->saveMassiveFile('attachedFile');
            }
            $createDate=date('Y-m-d H:i:s');
            $campain=null;
            if(empty($existCampain)){
                $campainModel->save([
                    'nombre' => $data->campainName,
                    'fechaCreacion' => $createDate,
                ]);
                $campain=$campainModel->insertID();
            }else{
                $campain=$existCampain->id;
            }
            $createDate=date('Y-m-d H:i:s');
            $saveData=[
                'asuntoNotificacion' => 'Notificación masiva',
                'idTipoNotificacion' => 6,
                'idRadicado'         => null,
                'idEstadoNotificacion'=> 4,
                'idFormato'          => $data->formatType!=''?$data->formatType:null,
                'idRequerimiento'    => $data->requirementType!=''?$data->requirementType:null,
                'idCategoria'        => $data->category!=''? $data->category:null,
                'idCampania'         => $campain,
                'anioReporte'        => $data->year? $data->year:null,
                'entidad'            => null,
                'trimestre'          => $data->quarter!=''?$data->quarter:null,
                'plazo'              => ($data->deadline!=''and $data->deadline!='null')?$data->deadline:null,
                'hora'               => $data->hour!=''?$data->hour:null,
                'sector'             => null,
                'textoNotificacion'  => $data->body !=''?$data->body:null,
                'adjunto'            => null,
                'fechaCreacion'      => $createDate,
                'para'               => null,
                'de'                 => $data->from,
                'archivoMasivo'      => $massiveFile,
            ];
            $historialCambiosModel= new HistorialCambiosModel();
            if(isset($exist->id) and $exist->id>0){
                $draftsModel->where('id',$exist->id)->set($saveData)->update();
                $historialCambiosModel->save([
                    'idBorrador' => $exist->id,
                    'nombre'     => $data->name,
                    'tipo'       => 'update',
                    'email'      => $data->emailSave,
                    'fechaCreacion' => $createDate,
                ]);
            }
            else{
                $draftsModel->save($saveData);
                $idNotification=$draftsModel->insertID();
                $historialCambiosModel->save([
                    'idBorrador' => $idNotification,
                    'nombre'     => $data->name,
                    'tipo'       => 'create',
                    'email'      => $data->emailSave,
                    'fechaCreacion' => $createDate,
                ]);
            }
            $this->log->info('Notificacion masiva guardada correctamente');
            return $this->getResponse([
                'message' => 'Notificacion masiva guardada correctamente',
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
    public function createMassiveNotification()
    {
        try{
            $this->log->info('Entrando a la creación de notificaciones masivas');
            $reminderModel= new RecordatoriosModel();
            $reminder=$reminderModel->find(1);
            if(!$reminder){
                throw new Exception('No se ha configurado los recordatorios, por favor comuniquese con la persona responsable de configuración',1);
            }
            $data=(object)$this->request->getVar(); 
            $data->from=$data->from??'usta@usta.com';
            if(count($data->mails)<=0){
                throw new Exception('Los destinatarios son requeridos',1);
            }
            $this->cleanMassiveData($data);
            $createDate=date('Y-m-d H:i:s');
            $campainModel= new CampaniaModel();
            $existCampain=$campainModel->asObject()->where('nombre',$data->campainName)->first();
            $campain=null;
            if(empty($existCampain)){
                $campainModel->save([
                    'nombre' => $data->campainName,
                    'fechaCreacion' => $createDate,
                ]);
                $campain=$campainModel->insertID();
            }else{
                $campain=$existCampain->id;
            }
            
            $notificationModel= new ModuloNotificacionesModel();
            $historialCambiosModel= new HistorialCambiosModel();
            $data->file=$this->saveAttachedFile("attachedFile");
            $changes=[];
            if(isset($data->id) and $data->id>0)
            {
                $changes=$historialCambiosModel->asObject()->where('idBorrador',$data->id)->findAll();

            }
            foreach($data->mails as $mail){
                $toModel= new DestinatarioModel();
                $existTo=$toModel->where('email',$mail->to)->first();
                if(empty($existTo)){
                    $toModel->save([
                        'email' => $mail->to,
                    ]);
                    $to=$toModel->insertID();
                }
                $caseNumber=$this->createCaseNumber();
                $notificationModel->save([
                    'asuntoNotificacion' => $mail->subject,
                    'idTipoNotificacion' => 6,
                    'idRadicado'         => $caseNumber->id,
                    'idEstadoNotificacion'=> 2,
                    'idFormato'          => $data->formatType,
                    'idRequerimiento'    => $data->requirementType,
                    'idCategoria'        => $data->category,
                    'idCampania'         => $campain,
                    'anioReporte'        => $data->year,
                    'entidad'            => null,
                    'trimestre'          => $data->quarter,
                    'plazo'              => $data->deadline,
                    'hora'               => $data->hour,
                    'sector'             => $mail->sector,
                    'textoNotificacion'  => $data->body,
                    'adjunto'            => $data->file,
                    'fechaCreacion'      => $createDate,
                ]);
                $notification=$notificationModel->insertID();
                $threadModel= new HiloRespuestaNotificacionModel();
                $threadModel->save([
                    'idNotificacion' => $notification,
                    'de'             => $data->from,
                    'para'           => $mail->to,
                    'mensaje'        => $data->body,
                    'fechaCreacion'  => $createDate,
                ]);
                $idThread=$threadModel->insertID();
                $hash=hash('sha256',$notification.$data->from.$mail->to.$data->campainName.$data->body);
                $blockchainModel= new BlockchainModel();
                $blockchainModel->save([
                    'idTipoNotificacion' => 6,
                    'idHiloRespuesta'    => $threadModel->insertID(),
                    'idAuditoria'        => null,
                    'hashGenerado'       => $hash,
                    'zilliqa'            => null,
                    'hashGenerado2'      => null,
                    'zilliqa2'           => null,
                    'fechaCreacion'      => $createDate,
                ]);
                $idBlockchain=$blockchainModel->insertID();
                $zilliqa=$this->hashBlockchain($hash,"hash1",$idBlockchain);
                $blockchainModel->where('id',$idBlockchain)->set(['zilliqa'=>$zilliqa])->update();
                $historialCambiosModel->save([
                    'idNotificacion' => $notification,
                    'nombre'         => $data->name,
                    'tipo'           => 'send',
                    'email'          => $data->emailSave,
                    'fechaCreacion'  => $createDate,
                ]);
                //buscar en el historial de cambios si hubieron cambios en el borrador
                if(isset($data->id)){
                    foreach($changes as $change){
                        $change->idNotificacion=$notification;
                        $change->idBorrador=null;
                        $historialCambiosModel->save([
                            'idNotificacion' => $notification,
                            'nombre'         => $change->nombre,
                            'tipo'           => $change->tipo,
                            'email'          => $change->email,
                            'fechaCreacion'  => $change->fechaCreacion,
                        ]);
                    }
                }
                $data->caseNumber=$caseNumber->radicado;
                $data->subject=$mail->subject;
                $data->sector=$mail->sector;
                $data->to=$mail->to;
                $data->hourCreate=date('h:i:s A');
                $data->date=$createDate;
                $data->entity='---';
                $statusOnbase=$this->saveDocument($data);
                if($statusOnbase->Code=='00'){
                    $threadModel->where('id',$idThread)
                                ->set(['estadoOnbase'=>1,'DocumentHandleOnbase'=>$statusOnbase->DocumentHandle])
                                ->update();
                }
            }
            //borrar el historial de cambios
            if(isset($data->id)){
                $historialCambiosModel->where('idBorrador',$data->id)->delete();
                $drafsModel= new BorradoresModel();
                $drafsModel->where('id',$data->id)->delete();
            }
            $this->log->info('Notificaciones masivas creadas correctamente');
            return $this->getResponse([
                'message' => 'Campaña creada correctamente',
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
    public function getFormValues(){
        try{
            $this->log->info('Entrando a la obtención de valores para el formulario');
            $vars=(Object)$this->request->getVar();
            if(!isset($vars->shippingTypeName) or $vars->shippingTypeName==""){
                throw new Exception('El tipo de notificación es requerido',1);
            } 
            $categoryModel= new CategoryModel();
            $categories=$categoryModel->select('id,nombre as name')
                                      ->where('estado',1)
                                      ->orderBy('nombre','ASC')
                                      ->asObject()
                                      ->findAll();
            $formaTypesModel= new FormatosModel();
            $formats=$formaTypesModel->select('id,nombreFormato as name')
                                      ->where('estado',1)
                                      ->orderBy('nombreFormato','ASC')
                                      ->asObject()
                                      ->findAll();
            $RequirementTypeModel= new TipoRequerimientoModel();
            $requirementTypes=$RequirementTypeModel->select('tiporequerimiento.id,nombre as name')
                                      ->join('tiponotificaciones','tiponotificaciones.id=tiporequerimiento.idTipoNotificaciones')  
                                      ->where('estado',1)
                                      ->where('tiponotificaciones.nombreTipo',$vars->shippingTypeName)
                                      ->orderBy('nombre','ASC')
                                      ->asObject()
                                      ->findAll();
            $reponse=[
                'message'          => 'OK',
                'categories'       => $categories,
                'formats'          => $formats,
                'requirementTypes' => $requirementTypes,
            ];
            if($vars->shippingTypeName!="Masivo" and $vars->action=="create"){
                $caseNumber=$this->createCaseNumber();
                $reponse['caseNumber']=$caseNumber->radicado;
            }  
            $this->log->info('Valores para el formulario cargados correctamente');                       
            return $this->getResponse($reponse);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function getFilterValues(){
        try{
            $this->log->info('Entrando a la obtención de valores para el filtro');
            $vars=(Object)$this->request->getVar();
            $categoryModel= new CategoryModel();
            $categories=$categoryModel->select('id,nombre as name')
                                      ->where('estado',1)
                                      ->orderBy('id','ASC')
                                      ->asObject()
                                      ->findAll();
            $formaTypesModel= new FormatosModel();
            $formats=$formaTypesModel->select('id,nombreFormato as name')
                                      ->where('estado',1)
                                      ->orderBy('id','ASC')
                                      ->asObject()
                                      ->findAll();
            $RequirementTypeModel= new TipoRequerimientoModel();
            $requirementTypes=$RequirementTypeModel->select('tiporequerimiento.id,nombre as name')
                                      ->join('tiponotificaciones','tiponotificaciones.id=tiporequerimiento.idTipoNotificaciones')  
                                      ->where('estado',1)
                                      ->orderBy('id','ASC')
                                      ->asObject()
                                      ->findAll();
            $this->log->info('Valores para el filtro cargados correctamente');
            return $this->getResponse([
                'message'          => 'OK',
                'categories'       => $categories,
                'formats'          => $formats,
                'requirementTypes' => $requirementTypes,
            ]);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function changeToArchived(){
        try{
            $this->log->info('Entrando al cambio de estado de notificaciones');
            $vars=$this->request->getVar();
            $ids=$vars->data;
            if(is_object($ids) and isset($ids->id) and intval($ids->id)>0){
                $this->archiveNotification($ids->id,$vars->rol);
                $this->log->info('Notificación archivada correctamente');
                return $this->getResponse([
                    'message' => 'Notificación archivada correctamente',
                ]);
            }
            if(is_array($ids) and count($ids)>0){
                foreach($ids as $id){
                    $this->archiveNotification(intval($id),$vars->rol);
                }
                $this->log->info('Notificaciones archivadas correctamente');
                return $this->getResponse([
                    'message' => 'Notificaciones archivadas correctamente',
                ]);
            }
            throw new Exception('El identificador o identificadores dados son inválidos',1);
        }catch(Exception $e){
            $message=$e->getMessage();
            if($e->getCode()==1){
                $this->log->info($message);
                return $this->getResponse(['message' => $message],HTTP_BAD_REQUEST);
            }
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function changeToMain(){
        try{
            $this->log->info('Entrando al cambio de estado de notificaciones');
            $vars=$this->request->getVar();
            $ids=$vars->data;
            if(is_object($ids) and isset($ids->id) and intval($ids->id)>0){
                $this->changeToMainNotification(intval($ids->id),$vars->rol);
                $this->log->info('Notificación enviada a la bandeja principal correctamente');
                return $this->getResponse([
                    'message' => 'Notificación enviada a la bandeja principal correctamente',
                ]);
            }
            if(is_array($ids) and count($ids)>0){
                foreach($ids as $id){
                    $this->changeToMainNotification(intval($id),$vars->rol);
                }
                $this->log->info('Notificaciones enviadas a la bandeja principal correctamente');
                return $this->getResponse([
                    'message' => 'Notificaciones enviadas a la bandeja principal correctamente',
                ]);
            }
            throw new Exception('El identificador o identificadores dados son inválidos',1);
        }catch(Exception $e){
            $message=$e->getMessage();
            if($e->getCode()==1){
                $this->log->info($message);
                return $this->getResponse(['message' => $message],HTTP_BAD_REQUEST);
            }
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function archiveNotification(int $id,string $rol){
        $this->log->info('Archivando notificación con id: '.$id);
        if($id<=0){
            throw new Exception('El identificador dado es inválido',1);
        }
        $notificationModel= new ModuloNotificacionesModel();
        $isClosed=$notificationModel->asObject()->where('id',$id)->first();
        if(empty($isClosed)){
            throw new Exception('La notificación no existe',1);
        }
        if(!($isClosed->idEstadoNotificacion==5)and $rol!='Agente'){
            if($isClosed->idEstadoNotificacion==3 and $rol!='Agente'){
                throw new Exception('La notificación ya esta archivada',1);
            }
            throw new Exception('La notificación no se puede archivar sin estar cerrada',1);
        }
        if(!($isClosed->idEstadoNotificacion==5)and $rol=='Agente'){
            if($isClosed->idEstadoNotificacion==3 and $isClosed->idEstadoNotificacionAgente==3 and $rol=='Agente'){
                throw new Exception('La notificación ya esta archivada',1);
            }
            if($isClosed->idEstadoNotificacion!=3){
                throw new Exception('La notificación no se puede archivar sin estar cerrada',1);
            }
        }
        if($isClosed->idEstadoNotificacionAgente==3 and $rol=='Agente'){
            throw new Exception('La notificación ya esta archivada',1);
        }
        $this->log->info('Cambiando estado de notificación con id: '.$id);
        if($rol=='Agente'){
            $notificationModel->where('id',$id)->set(['idEstadoNotificacionAgente'=>3])->update();
        }else{
            $notificationModel->where('id',$id)->set(['idEstadoNotificacion'=>3])->update();   
        } 
    }
    public function changeToMainNotification(int $id,string $rol){
        $this->log->info('Enviando a la bandeja principal la notificación con id: '.$id);
        if($id<=0){
            throw new Exception('El identificador dado es inválido',1);
        }
        $notificationModel= new ModuloNotificacionesModel();
        $isClosed=$notificationModel->asObject()->where('id',$id)->first();
        if(empty($isClosed)){
            throw new Exception('La notificación no existe',1);
        }
        if($isClosed->idEstadoNotificacion==2 and $rol!='Agente'){
            throw new Exception('La notificación ya esta en la bandeja principal',1);
        }
        if($isClosed->idEstadoNotificacionAgente==null and $rol=='Agente'){
            throw new Exception('La notificación ya esta en la bandeja principal',1);
        }
        $this->log->info('Cambiando estado de notificación con id: '.$id);
        if($rol=='Agente'){
            $notificationModel->where('id',$id)->set(['idEstadoNotificacionAgente'=>null])->update();
        }else{
            $notificationModel->where('id',$id)->set(['idEstadoNotificacion'=>2])->update(); 
        }   
    }
    public function hashBlockchain(string $hash,string $type,$idBlockchain){
        $this->log->info('Entrando a la creación de hash en blockchain');
        $isActivated=getenv('ACTIVATE_BLOCKCHAIN');
        if($isActivated=="false") {
            return '8293fedac191f10119e2a44db02c5be33e3aba13cbf6c774b66c6b78a063fb7b';
        } 
        $url =getenv('URL_BLOCKCHAIN').'/blockchain/contract/transaction/create/v2';
        $callback=base_url().'api/blockchain/saveHash/'.$type.'/'.$idBlockchain;        
        $this->log->info('url blockchain: '.$url);
        $this->log->info('callback blockchain: '.$callback);
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'transaction_object' => '{"hash":"'.$hash.'"}',
            'contract'           => '1',
            'transition'         => 'saveHash',
            'callback'           => $callback,
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Token '.getenv('TOKEN_BLOCKCHAIN'),
            'content-type: multipart/form-data; boundary=---011000010111000001101001\''
        ),
        ));
        $response = curl_exec($curl);
        $errorCur=curl_error($curl);
        curl_close($curl);
        $response=json_decode($response);
        if($errorCur){
            $this->log->error('Error al crear hash en blockchain, error: '.$errorCur);
        }
        if(isset($response->error)){
            throw new Exception($response->error,1);
        }
        $this->log->info('Hash creado correctamente');
        return $response->txn_info;
    }
    public function isValidHash($hash){
        try{
            $blockchainModel= new BlockchainModel();
            $exist=$blockchainModel->where('zilliqa',$hash)->first();
            if(empty($exist)){
                return $this->getResponse([
                    'message' => 'El hash no existe',
                    'exist'   => false,
                ]);
            }
            return $this->getResponse([
                'message' => 'El hash existe',
                'exist'   => true,
            ]);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }

    }
    public function setStatusReaded(){
        try{
            $this->log->info('Entrando al cambio de estado de notificaciones');
            $vars=(Object)$this->request->getVar();
            $now=date('Y-m-d H:i:s');
            
            $thread= new HiloRespuestaNotificacionModel();
            $blockchains=$thread->asObject()
                ->select('blockchain.id,blockchain.hashGenerado')
                ->join('blockchain','blockchain.idHiloRespuesta=hilorespuestanotificacion.id')
                ->whereIn('blockchain.id',$vars->idBlockchains)
                ->findAll();
            foreach($blockchains as $blockchain){
                $blockchainModel= new BlockchainModel();
                $hash=hash('sha256',$blockchain->hashGenerado.$now);
                $blockchainModel->where('id',$blockchain->id)
                                ->set(['hashGenerado2'=>$hash])
                                ->update();
                $zilliqa2=$this->hashBlockchain($hash,"hash2",$blockchain->id);
                $blockchainModel->where('id',$blockchain->id)->set(['zilliqa2'=>$zilliqa2])->update();
            }
            $this->log->info('Estado de notificaciones cambiado correctamente');
            return $this->getResponse([
                'message' => 'OK',
                'items'   => $blockchains,
            ]);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function setReply(){
        try{
            $this->log->info('Entrando al envio de mensajes');
            $vars=(Object)$this->request->getVar();
            $threadModel= new HiloRespuestaNotificacionModel();
            if($vars->idNotification<=0 or empty($vars->idNotification)){
                throw new Exception('El id de la notificacion es requerido',1);
            }
            if($vars->from==""){
                throw new Exception('El remitente es requerido',1);
            }
            if($vars->to==""){
                throw new Exception('El destinatario es requerido',1);
            }
            if($vars->body=="" or $vars->body=="<p><br></p>"){
                throw new Exception('El cuerpo del mensaje es requerido',1);
            }
            $position=$threadModel->asObject()
                                  ->select('MAX(posicion) as position')
                                  ->where('idNotificacion',$vars->idNotification)
                                  ->first();
            $Agent=$threadModel->asObject()
                                  ->select('para as email')
                                  ->where('idNotificacion',$vars->idNotification)
                                  ->orderBy('id','ASC')
                                  ->first();
            $existToChageStatus=$threadModel->asObject()
                                            ->where('idNotificacion',$vars->idNotification);
            if($Agent->email==$vars->to){
                $existToChageStatus->where('de',$vars->to);
            }else{
                $existToChageStatus->where('de <>',$Agent->email);
            }
            $existToChageStatus=$existToChageStatus->where('estado',0)
                                                   ->set(['estado'=>1])
                                                   ->update();
            $position=$position->position+1;
            $vars->file=$this->saveAttachedFile($vars->caseNumber);
            $vars->date=date('Y-m-d H:i:s');
            $moduleNotificationModel= new ModuloNotificacionesModel();
            $notification=$moduleNotificationModel->asObject()->where('id',$vars->idNotification)->first();
            $blockchainModel= new BlockchainModel();
            $vars->subject=$notification->asuntoNotificacion;
            $vars->entity=$notification->entidad;
            $vars->hourCreate=date('h:i:s A');
            $hash=hash('sha256',$vars->idNotification.$vars->from.$vars->to.$vars->body);
            $threadModel->save([
                'idNotificacion' => $vars->idNotification,
                'de'             => $vars->from,
                'para'           => $vars->to,
                'mensaje'        => $vars->body,
                'posicion'       => $position,
                'adjunto'        => $vars->file,
                'fechaCreacion'  => $vars->date,
            ]);
            $idThread=$threadModel->insertID();
            
            $blockchainModel->save([
                'idTipoNotificacion' => $notification->idTipoNotificacion,
                'idHiloRespuesta'    => $idThread,
                'idAuditoria'        => null,
                'hashGenerado'       => $hash,
                'zilliqa'            => null,
                'hashGenerado2'      => null,
                'zilliqa2'           => null,
                'fechaCreacion'      => $vars->date,
            ]);
            $idBlockchain=$blockchainModel->insertID();
            $zilliqa=$this->hashBlockchain($hash,"hash1",$idBlockchain);
            $blockchainModel->where('id',$idBlockchain)->set(['zilliqa'=>$zilliqa])->update();
            $statusOnbase=$this->saveDocument($vars,'Respuesta Informes de los Operadores');
            if($statusOnbase->Code=='00'){
                $threadModel->where('id',$idThread)
                            ->set(['estadoOnbase'=>1,'DocumentHandleOnbase'=>$statusOnbase->DocumentHandle])
                            ->update();
            }
            $this->log->info('Mensaje enviado correctamente');
            return $this->getResponse([
                'message' => 'Mensaje enviado correctamente',
            ]);
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
    public function saveDocument($params,$type='Solicitud Informes a los Operadores'){
        $isActivated=getenv('ACTIVATE_SOAP_ONBASE');
        if($isActivated=="false") {
            return (object)[
                'Code'=>'00','Message'=>'Documento creado correctamente',
                'DocumentHandle'=>'1234567890'
            ];
        }
        $this->log->info('Entrando a la creación de documento en OnBase');
        $soapLibrary = new SoapServices();
        $dataPdf=(object)[
            'RadicacionEntrada'  => $params->caseNumber,
            'Fecha'     => date_format(date_create($params->date),'d/m/Y'),
            'Hora'      => $params->hourCreate,
            'Asunto'    => $params->subject,
            'MailFrom'  => $params->from,
            'Remitente' => $params->entity??'CRC entidad',
            'MailTo'    => $params->to,
            'Destino'   => 'Usuario CRC',
            'Mensaje'   => $params->body,
        ];
        $pdf=$this->convertToPdf($dataPdf,'Respuesta');
        $params=[
            'DiskGroupName' => 'Datos',
            'DocumentTypeName' => $type,
            'Keywords' => [
                'Radicacion Entrada' => $params->caseNumber,
                'Fecha'     => date_format(date_create($params->date),'d/m/Y'),
                'Hora'      => $params->hourCreate,
                'Asunto'    => $params->subject,
                'MAIL From' => $params->from,
                'Remitente' => $params->entity??'CRC entidad',
                'MAIL To'   => $params->to,
                'Destino'   => 'Usuario CRC',
            ],
            'pdf' => $pdf['pdfBase64'],
        ];
        $response = (object)$soapLibrary->saveDocument($params);
        $this->log->info('Documento creado correctamente');
        return $response;
    }
    public function createBodyPdf(object $data){
        $body='';
        $body.='<table border="1" cellpadding="5" cellspacing="0" style="width:100%;border-collapse:collapse;">';
        $body.='<tr>';
        $body.='<td style="width:20%;"><b>Radicacion de entrada</b></td>';
        $body.='<td style="width:30%;">'.$data->RadicacionEntrada.'</td>';
        $body.='<td style="width:20%;"><b>Fecha</b></td>';
        $body.='<td style="width:30%;">'.$data->Fecha.'</td>';
        $body.='</tr>';
        $body.='<tr>';
        $body.='<td style="width:20%;"><b>Hora</b></td>';
        $body.='<td style="width:30%;">'.$data->Hora.'</td>';
        $body.='<td style="width:20%;"><b>Asunto</b></td>';
        $body.='<td style="width:30%;">'.$data->Asunto.'</td>';
        $body.='</tr>';
        $body.='<tr>';
        $body.='<td style="width:20%;"><b>Mail From</b></td>';
        $body.='<td style="width:30%;">'.$data->MailFrom.'</td>';
        $body.='<td style="width:20%;"><b>Remitente</b></td>';
        $body.='<td style="width:30%;">'.$data->Remitente.'</td>';
        $body.='</tr>';
        $body.='<tr>';
        $body.='<td style="width:20%;"><b>Mail To</b></td>';
        $body.='<td style="width:30%;">'.$data->MailTo.'</td>';
        $body.='<td style="width:20%;"><b>Destino</b></td>';
        $body.='<td style="width:30%;">'.$data->Destino.'</td>';
        $body.='</tr>';
        $body.='</table>';
        $body.='<br>';
        $body.='<br>';
        $body.=$data->Mensaje;
        return $body;
    }
    public function convertToPdf(object $data, string $type){
        try{
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('USTA');
            $pdf->SetTitle($type);
            $pdf->SetSubject('Notificacion');
            $pdf->SetKeywords('Notificacion');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(10, 10, 10, true);
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->AddPage();
            $body=$this->createBodyPdf($data);
            $pdf->writeHTML($body, true, false, true, false, '');
            $path = WRITEPATH . 'notifications/onBase' . DIRECTORY_SEPARATOR;
            $this->createDirectory($path);
            $name = $data->RadicacionEntrada . '_' . date('YmdHis') . '.pdf';
            $pdf->Output($path . $name, 'F');
            //convertir el documento en base64
            $bodyBase64 =  base64_encode($body);
            return [
                'base64' => $bodyBase64,
                'pdf'   => $pdf->Output($path . $name, 'S'),//obtener el documento en pdf
                'pdfBase64' => base64_encode($pdf->Output($path . $name, 'S')),//obtener el documento en pdf en base64
            ];
        }catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
        }
    }
    
    public function changeStatus(){
        try {
            $this->log->info('Entrando al cambio de estado de notificaciones');
            $vars=(Object)$this->request->getVar();
            $notificationModel= new ModuloNotificacionesModel();
            $notification=$notificationModel->asObject()
                                            ->where('id',$vars->idNotification)
                                            ->first();
            if(empty($notification)){
                throw new Exception('La notificación no existe',1);
            }            
            $dataToupdate=[
                'idEstadoNotificacion'=>$vars->idStatus,
                'plazo' => $vars->deadline,
                'hora'  => $vars->hour,
                'observaciones' => $vars->observations??'',

            ];
            if($vars->deadline>$notification->plazo){
                $dataToupdate['plazoExtendido']=1;
            }
            $notificationModel->where('id',$vars->idNotification)
                            ->set($dataToupdate)
                            ->update();
            if($notification->idEstadoNotificacionAgente==3 and $vars->idStatus==2){
                $notificationModel->where('id',$vars->idNotification)
                            ->set(['idEstadoNotificacionAgente'=>null])
                            ->update();
            }
            $this->log->info('Estado de notificación cambiado correctamente');
            return $this->getResponse([
                'message' => 'Plazo actualizado correctamente',
            ]);
        } catch(Exception $e){
            $message=$e->getMessage();
            $this->log->error($message);
        }
        
    }
    public function downLoadFile(){
        $name=$this->request->getVar('name');
        $path = WRITEPATH . 'notifications/attachedFile' . DIRECTORY_SEPARATOR;
        $file = $path.$name;
        $type = mime_content_type($file);
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: '.$type);
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }
    public function saveAttachedFile(string $fileName){
        $file=$this->request->getFile('attachedFile');
        if(!$file or !$file->isValid()){
           return null; 
        }
        $path = WRITEPATH . 'notifications/attachedFile' . DIRECTORY_SEPARATOR;
        $this->createDirectory($path);
        $extension=explode('.',$file->getName());
        $extension=$extension[count($extension)-1];
        $name = $fileName . '_' . date('YmdHis') . '.'.$extension;
        $file->move($path, $name);
        return $name;
    }
    public function saveMassiveFile(string $fileName){
        $file=$this->request->getFile('file');
        if(!$file or !$file->isValid()){
           return null; 
        }
        $path = WRITEPATH . 'notifications/massive' . DIRECTORY_SEPARATOR;
        $this->createDirectory($path);
        $extension=explode('.',$file->getName());
        $extension=$extension[count($extension)-1];
        $name = $fileName . '_' . date('YmdHis') . '.'.$extension;
        $file->move($path, $name);
        return $name;
    }
    public function getCaseNumber($caseNumber){
        $this->log->info('Buscando el numero de radicado: '.$caseNumber);
        $caseNumberModel= new RadicadosModel();
        $caseNumber=$caseNumberModel->asObject()
                                    ->select('id,radicado')
                                    ->where('radicado',$caseNumber)
                                    ->first();
        if(!isset($caseNumber->id)){
            throw new Exception('El numero de radicado no existe',1);
        }
        $this->log->info('Numero de radicado encontrado');
        return $caseNumber;
    }
    public function createCaseNumber(){
        $this->log->info('Creando numero de radicado');
        $currentYearModel= new AnioActualModel();
        $caseNumberModel= new RadicadosModel();
        $currentYear=date('Y');
        $lastCaseNumber=$caseNumberModel->asObject()
                                        ->select('id,radicado')
                                        ->orderBy('id','DESC')
                                        ->first();       
        $storeYear=$currentYearModel->asObject()
                                    ->select('anio')
                                    ->orderBy('anio','DESC')
                                    ->first();
        $caseNumber=intval($currentYear.INITIAL_CASE_NUMBER_SUFFIX);
        if(isset($lastCaseNumber->radicado)){
            $caseNumber=intval($lastCaseNumber->radicado);
        }
        $caseNumber++;
        if($storeYear->anio!=$currentYear){
            $currentYearModel->save([
                'id'=>1,
                'anio'=>$currentYear
            ]);
            $caseNumber=intval($currentYear.(INITIAL_CASE_NUMBER_SUFFIX+INCREMENT_FOR_NEW_YEAR));
        }
        $caseNumberModel->save([
            'radicado'=>$caseNumber,
            'fechaCreacion'=>date('Y-m-d H:i:s')
        ]);
        $this->log->info('Numero de radicado creado correctamente');
        return (object)[
            'id'=>$caseNumberModel->insertID(),
            'radicado'=>$caseNumber
        ];
    }
    public function saveDocumentOnbase(){
        $this->log->info('Entrando a la creación de documento en OnBase');
        $notificationsModel= new ModuloNotificacionesModel();
        $notificationsModel->select('r.radicado as RadicacionEntrada,
                                    DATE_FORMAT(modulonotificaciones.fechaCreacion,"%d/%m/%Y") as Fecha,
                                    modulonotificaciones.asuntoNotificacion as Asunto,
                                    modulonotificaciones.entidad as Remitente,
                                    TIME_FORMAT(TIME(hr.fechaCreacion),"%h:%i %p") as Hora,
                                    hr.id as idHiloRespuesta,
                                    hr.de as MailFrom,
                                    hr.para as MailTo,
                                    hr.fechaCreacion as FechaCreacion,
                                    "Usuario CRC" as Destino,
                                    modulonotificaciones.textoNotificacion as Mensaje
                                    ')
                           ->join('hilorespuestanotificacion hr','hr.idNotificacion=modulonotificaciones.id')
                           ->join('radicados r','r.id=modulonotificaciones.idRadicado')
                           ->where('hr.estadoOnbase',0);
        $notifications=$notificationsModel->asObject()->findAll();
        if(empty($notifications)) return;
        $soapLibrary = new SoapServices();
        foreach($notifications as $notification){
            $body=$this->createBodyPdf($notification);
            $bodyBase64 =  base64_encode($body);
            $params=[
                'DiskGroupName' => 'Datos',
                'DocumentTypeName' => 'Solicitud Informes a los Operadores',
                'Keywords' => [
                    'Radicacion Entrada' => $notification->RadicacionEntrada,
                    'Fecha' => date_format(date_create($notification->FechaCreacion),'d/m/Y'),
                    'Hora' => $notification->Hora,
                    'Asunto' => $notification->Asunto,
                    'MAIL From' => $notification->MailFrom,
                    'Remitente' => $notification->Remitente??'CRC entidad',
                    'MAIL To' => $notification->MailTo,
                    'Destino' => 'Usuario CRC',
                ],
                'pdf' => $bodyBase64,
            ];
            $response = (object)$soapLibrary->saveDocument($params);
            if($response->code!='00'){
                $this->log->error('Error al guardar el documento en Onbase: '.$response->message);
                return;
            }
            $this->log->info('Documento creado correctamente');
            $notificationsModel->where('id',$notification->idHiloRespuesta)
                               ->set(['estadoOnbase'=>1])
                               ->update();
        }

    }
    public function deleteDrafts(){
        try{
            $this->log->info('Entrando a la eliminación de borradores');
            $vars=$this->request->getVar();
            if(count($vars)<=0){
                throw new Exception('Los identificadores son requeridos',1);
            }
            $draftsModel= new BorradoresModel();
            $draftsModel->whereIn('id',$vars)->delete();
            $this->log->info('Borradores eliminados correctamente');
            return $this->getResponse([
                'message' => 'Borradores eliminados correctamente',
            ]);
        }catch(Exception $e){
            $message=$e->getMessage();
            if($e->getCode()==1){
                $this->log->info($message);
                return $this->getResponse(['message' => $message],HTTP_BAD_REQUEST);
            }
            $this->log->error($message);
            return $this->failServerError('Ha ocurrido un error en el servidor');
        }
    }
}
