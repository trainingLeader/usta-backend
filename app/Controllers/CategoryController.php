<?php

namespace App\Controllers;

use Exception;
use ReflectionException;
use App\Interfaces\ICRUD;
use App\Models\{CategoryModel};
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Libraries\SupportLogHandler;
use App\Models\ModuloNotificacionesModel;

class CategoryController extends BaseController implements ICRUD
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
        $categoryModel= new CategoryModel();
        $categoryModel->select('id,nombre as name,estado as status');
        if(!empty($filter))$categoryModel->where($filter);
        $categoryModel->orderBy('id','ASC');
        $categories=$categoryModel->asObject()->paginate(intval($perPage), 'default', intVal($page));
        return $this->getResponse([
            'message'      => 'OK',
            'items'        => $categories,
            'totalRecords' => $categoryModel->pager->getTotal(),
            'totalPages'   => $categoryModel->pager->getPageCount(),
        ]);
    }
    public function show($id)
    {
        try{
            $categoryModel= new CategoryModel();
            $category=$categoryModel->asObject()->find($id);
            if(empty($category)){
                throw new Exception('No se encontro la categoria',1);
            }
            return $this->getResponse([
                'message' => 'OK',
                'category' => $category,
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
            $categoryModel= new CategoryModel();
            $existCategory=$categoryModel->where('nombre',$data->name)
                          ->first();
            if(!empty($existCategory))throw new Exception('La cagoria ya existe',1);
            $categoryModel->save(['nombre'=>$data->name]);
            return $this->getResponse([
                'message' => 'Categoria creado correctamente',
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
            $existNotification=$notificacionesModel->where('idCategoria',$id)
                                                  ->first();
            if(!empty($existNotification))throw new Exception('No se puede editar la categoria, esta siendo usada en notificaciones',1);    
            $categoryModel= new CategoryModel();
            $updateCategory=$categoryModel->find($id);
            if(empty($updateCategory))throw new Exception('No se encontro la categoria',1);
            $existCategory=$categoryModel->where('nombre',$vars->name)
                                         ->first();
            if(isset($existCategory) and $existCategory->id!=$id)throw new Exception('La cagoria ya existe',1);
            $updateCategory->name=$vars->name;
            $updateCategory->status=$vars->status;
            $updateCategory->updatedAt=date('Y-m-d H:i:s');
            $categoryModel->save($updateCategory);
            return $this->getResponse([
                'message' => 'Categoria actualizado correctamente',
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
            $categoryModel= new CategoryModel();
            $deleteCategory=$categoryModel->find($id);
            if(empty($deleteCategory))throw new Exception('No se encontro la categoria',1);
            $categoryModel->delete($id);
            return $this->getResponse([
                'message' => 'Categoria eliminada correctamente',
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
