<?php

namespace App\Controllers;

use Exception;
use App\Entities\Rol;
use ReflectionException;
use App\Interfaces\ICRUD;
use App\Controllers\BaseController;
use App\Libraries\SupportLogHandler;
use App\Models\PermisosGenericosModel;
use App\Models\{RolModel,GenericosVsSubmodulosModel,SubmodulosModel,ModulosMaestrosModel};

class RolController extends BaseController implements ICRUD
{
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
        $rolModel= new RolModel();
        $rolModel->select('id,nombre as name');
        if(!empty($filter))$rolModel->like($filter);
        $rol=$rolModel->asObject()->paginate(intval($perPage), 'default', intVal($page));
        return $this->getResponse([
            'message'      => 'OK',
            'items'        => $rol,
            'totalRecords' => $rolModel->pager->getTotal(),
            'totalPages'   => $rolModel->pager->getPageCount(),
        ]);
    }
    public function show($id)
    {
        try{
            $rolModel= new RolModel();
            $rol=$rolModel->find($id);
            if(empty($rol)){
                throw new Exception('No se encontro el rol',1);
            }
            return $this->getResponse([
                'message' => 'OK',
                'rol' => $rol,
            ]);

        }catch(Exception $e){
            if($e->getCode()==1){
                return $this->getResponse([
                    'message' => $e->getMessage(),
                ],HTTP_NOT_FOUND);
            }
            return $this->getResponse([
                'message' => $e->getMessage(),
            ],HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function create()
    {
        try{
            $data=(object)$this->request->getVar(); 
            if(!isset($data->name)) throw new Exception('El nombre es requerido',1);    
            $rolModel= new RolModel();
            $existRol=$rolModel->where('nombre',$data->name)
                          ->first();
            if(!empty($existRol))throw new Exception('El rol ya existe',1);
            $rolModel->save(['nombre'=>$data->name]);
            return $this->getResponse([
                'message' => 'Rol creado correctamente',
            ]);
        }
        catch(Exception $e){
            if($e->getCode()==1){
                $message=$e->getMessage();
                $this->log->message('info',$message);
                return $this->getResponse([
                    'message' => $message,
                ],HTTP_BAD_REQUEST);
            }
            $this->log->message('error',$e->getMessage());
            return $this->getResponse([
                'message' => $e->getMessage(),
            ],HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function update()
    {
        try{
            $vars=(Object)$this->request->getVar();
            $id=$vars->id;
            $data=(object)$this->request->getVar(); 
            if(!isset($data->name)) throw new Exception('El nombre es requerido',1);    
            $rolModel= new RolModel();
            $updateRol=$rolModel->find($id);
            if(empty($updateRol))throw new Exception('No se encontro el rol',1);
            $existRol=$rolModel->where('nombre',$data->name)
                               ->first();
            if(isset($existRol) and $existRol->id!=$id)throw new Exception('El rol ya existe',1);
            $updateRol=(object)[
                'id' => $id,
                'nombre' => $data->name,
                'fechaModificacion' => date('Y-m-d H:i:s'),
            ];
            $rolModel->save($updateRol);
            return $this->getResponse([
                'message' => 'Rol actualizado correctamente',
            ]);
        }
        catch(Exception $e){
            $message=$e->getMessage();
            if($message=='There is no data to update.')$message='No hay datos para actualizar';
            if($e->getCode()==1 or $message=='No hay datos para actualizar'){
                $this->log->message('info',$message);
                return $this->getResponse([
                    'message' => $message,
                ],HTTP_BAD_REQUEST);
            }
            $this->log->message('error',$message);
            return $this->getResponse([
                'message' => $message,
            ],HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function delete($id)
    {
        try{ 
            $rolModel= new RolModel();
            $deleteRol=$rolModel->find($id);
            if(empty($deleteRol))throw new Exception('No se encontro el rol',1);
            $rolModel->delete($id);
            return $this->getResponse([
                'message' => 'Rol eliminado correctamente',
            ]);
        }
        catch(Exception $e){
            $message=$e->getMessage();
            if($e->getCode()==1){
                $this->log->message('info',$message);
                return $this->getResponse([
                    'message' => $message,
                ],HTTP_BAD_REQUEST);
            }
            $this->log->message('error',$message);
            return $this->getResponse([
                'message' => $e->getMessage(),
            ],HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function savePermissions()
    {
        try{
            $vars=(Object)$this->request->getVar();
            $permissions=$vars->params->approvals??[];
            $submodulosModel= new SubmodulosModel();
            $permisosModel= new PermisosGenericosModel();
            $genericosVsSubmodulosModel= new GenericosVsSubmodulosModel();
            $rolModel= new RolModel();
            $rol=$rolModel->find($vars->params->idRol);
            if(empty($rol))throw new Exception('No se encontro el rol',1);
            //validar que el idRol, idSubmodulos y idGenericos sean validos
            foreach($permissions as $permission){
                if(!isset($permission->idSubmodule))throw new Exception('El idSubmodule es requerido',1);
                if(!isset($permission->idGenerico))throw new Exception('El idGenerico es requerido',1);
                $submodule=$submodulosModel->find($permission->idSubmodule);
                if(empty($submodule))throw new Exception('No se encontro el submodulo',1);
                $permission=$permisosModel->find($permission->idGenerico);
                if(empty($permission))throw new Exception('El permiso dado no es válido',1);
            }

            $approvedPermissions=$genericosVsSubmodulosModel->asObject()
                                                            ->select('id,idSubmodulos,idGenericos')
                                                            ->where('idRol',$vars->params->idRol)
                                                            ->findAll();
            //Eliminar de $permissions los que ya tengo en base de datos
            $savePermissions=array_filter($permissions,function($item) use ($approvedPermissions){
                $exist=array_filter($approvedPermissions,function($approved) use ($item){
                    return $approved->idSubmodulos==$item->idSubmodule and $approved->idGenericos==$item->idGenerico;
                });
                return empty($exist);
            });
            //eliminar los permisos que estan en base de datos y no estan en $permissions, eliminarlos de la base de datos
            $deletePermissions=array_filter($approvedPermissions,function($item) use ($permissions){
                $exist=array_filter($permissions,function($permission) use ($item){
                    return $permission->idSubmodule==$item->idSubmodulos and $permission->idGenerico==$item->idGenericos;
                });
                return empty($exist);
            });
            if(!empty($deletePermissions)){
                $deletePermissions=array_map(function($item){
                    return $item->id;
                },$deletePermissions);
                $genericosVsSubmodulosModel->whereIn('id',$deletePermissions)->delete();

                
            }
            if(empty($savePermissions))return $this->getResponse([
                'message' => 'Permisos actualizados correctamente',
            ]);
            //hacer un insertbash de los permisos que estan en $permissions 
            //reiniciar los index de $savePermissions
            $savePermissions=array_values($savePermissions);
            $insertPermissions=array_map(function($item) use ($savePermissions){
                return [
                    'idRol'         => $savePermissions[0]->idRol,
                    'idSubmodulos'  => $item->idSubmodule,
                    'idGenericos'   => $item->idGenerico,
                    'fechaCreacion' => date('Y-m-d H:i:s'),
                ];
            },$savePermissions);
            $genericosVsSubmodulosModel->insertBatch($insertPermissions);
            $approvedPermissions=$genericosVsSubmodulosModel->asObject()
                                                            ->select('idGenericos as idGenerico,idSubmodulos as idSubmodule')
                                                            ->where('idRol',$rol->id)
                                                            ->findAll();
            $permissions=$approvedPermissions;
            return $this->getResponse([
                'message' => 'Permisos actualizados correctamente',
                'permissions' => $permissions,
            ]);

        }
        catch(Exception $e){
            $message=$e->getMessage();
            if($e->getCode()==1){
                $this->log->message('info',$message);
                return $this->getResponse([
                    'message' => $message,
                ],HTTP_BAD_REQUEST);
            }
            $this->log->message('error',$message);
            return $this->getResponse([
                'message' => $e->getMessage(),
            ],HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

}
