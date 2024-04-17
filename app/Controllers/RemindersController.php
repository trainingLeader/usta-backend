<?php

namespace App\Controllers;

use App\Models\ModuloNotificacionesModel;
use Exception;
use HTMLPurifier;
use HTMLPurifier_Config;
use ReflectionException;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Libraries\SupportLogHandler;
use App\Models\{RecordatoriosModel};
use App\Models\NotificationViewModel;

class RemindersController extends BaseController 
{
    use ResponseTrait;
    public function index()
    {
        
    }
    public function list()
    {
        $reminderModel= new RecordatoriosModel();
        $reminder=$reminderModel->first();
        return $this->getResponse([
            'message' => 'OK',
            'days' => $reminder['diasSemaforizacion']??"",
            'daysMail' => $reminder['diasRecordatorioMail']??"",
            'daysSMS' => $reminder['diasRecordatorioSMS']??"",
            'templateMail' => $reminder['plantillaMail']??"",
            'templateSMS' => $reminder['plantillaSMS']??"",
        ]);
    }
    public function update()
    {
        try{
            $reminderModel= new RecordatoriosModel();
            $data =(object) $this->request->getVar();
            $reminders=$data->params->reminders;
            $updateReminders=[
                'diasSemaforizacion'   => $reminders->trafficLightDays,
                'diasRecordatorioMail' => $reminders->mail,
                'diasRecordatorioSMS'  => $reminders->sms,
                'plantillaMail'        => $reminders->templateMail,
                'plantillaSMS'         => $reminders->templateSMS,
                'fechaModificacion'    => date('Y-m-d H:i:s'),
            ];
            $reminder=$reminderModel->first();
            if($reminder){
                $updateReminders['id']=$reminder['id'];
            }
            $reminderModel->save($updateReminders);
            return $this->getResponse([
                'message' => 'OK',
            ]);
        }
        catch(Exception $e){
            $this->log->error($e->getMessage());
            return $this->getResponse([
                'message' => 'ERROR',
            ]);
        }
    }
    
    public function sendReminders(){
        try{
            $reminderModel = new RecordatoriosModel();
            $reminder      = $reminderModel->find(1);
            $templateMail  = $reminder['plantillaMail'];
            $config        = HTMLPurifier_Config::createDefault();
            $purifier      = new HTMLPurifier($config);
            $templateMail  = $purifier->purify($templateMail);
            $templateMail  = str_replace('[[NOMBRE]]', 'Señor usuario de la usta', $templateMail);
            $email = \Config\Services::email();
            $notificationsModel = new NotificationViewModel();
            $moduleNotifications= new ModuloNotificacionesModel();
            $notificationsModel->select('id, to, subject, message, daysToEnd')
                            ->where('reminderSent=0')
                            ->where('message<>', 'Notificación cerrada')
                            ->where('message<>', 'El plazo ha vencido')
                            ->where('daysToEnd <=', $reminder['diasRecordatorioMail'])
                            ->where('`to` IS NOT NULL')
                            ->orderBy('daysToEnd', 'ASC');
            $notificationsTosend = $notificationsModel->findAll();
            $this->log->info('Notifications to send ' . json_encode($notificationsTosend));
            foreach ($notificationsTosend as $notification) {
                $templateMail  = str_replace('[[MENSAJE]]',$notification['message'] , $templateMail);
                $email->setTo($notification['to']);
                $email->setSubject('Recordatorio de notificación');
                $email->setMessage($templateMail);
                $isSend = $email->send();
                $this->log->info('Email is successfully sent ' . $isSend);
                if ($isSend) {
                    $this->log->info('Email is successfully sent ' . $isSend);
                    $moduleNotifications->update($notification['id'], ['recordatorioEnviado' => 1, 'fechaEnvioMail' => date('Y-m-d H:i:s')]);
                } else {
                    $data = $email->printDebugger(['headers']);
                    $this->log->error($data);
                }
            }
            return 'Email successfully sent';

        }catch(Exception $e){
            $this->log->error($e->getMessage());
            return 'Email not sent';
        }
    }
    public function saveImages(){
        $image = $this->request->getFile('upload');
        $path = WRITEPATH.'uploads/';
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        $nameNoExt=$image->getName();
        $ext=$image->getExtension();
        $nameNoExt=str_replace('.'.$ext,'',$nameNoExt);
        $newName=$nameNoExt.'-'.date('YmdHis').'-'.rand(1000,9999).'.'.$ext;
        $image->move($path,$newName);
        //obtener la url del archivo
        $urlImage=base_url().'uploads/'.$newName;
        return $this->getResponse([
            $urlImage
        ]);

    }

}
