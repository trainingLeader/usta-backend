<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Interfaces\ICRUD;
use Exception;
use ReflectionException;
use App\Models\{BorradoresModel};
use App\Libraries\SupportLogHandler;

class ImageController extends BaseController
{
    public function upload()
    {
        try{
            $file = $this->request->getFile('upload');
            if (!$file->isValid() && !$file->hasMoved()) {
                throw new Exception("Error al subir el archivo", 1);
            }
            //validamos que el archivo sea una imagen y que no pese mas de 300kb
            if (!in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/gif'])) {
                throw new Exception("El archivo no es una imagen", 1);
            }
            if ($file->getSize() > 300000) {
                throw new Exception("El archivo es demasiado grande", 1);
            }
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $newName);
            $data = [
                "uploaded" => 1,
                "fileName" => $newName,
                "url" =>base_url() .'api/uploads/' . $newName
            ];
            return $this->response->setJSON($data);

        }
        catch(Exception $e){
            $log = new SupportLogHandler();
            $log->error($e->getMessage());
            if($e->getCode() == 1){
                $error= 
                [
                    "error" => [
                        "message" => $e->getMessage()
                    ]
                ];
                return $this->response->setJSON($error);
            }
            return $this->response->setStatusCode(500);
        }
    }

    public function read($filename)
    {
        $file = WRITEPATH . 'uploads/' . $filename;
        if (!file_exists($file)) {
            return $this->response->setStatusCode(404);
        }
        $mime = mime_content_type($file);
        $this->response->setHeader('Content-Type', $mime);
        return readfile($file);
        
    }
}
