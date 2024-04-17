<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Interfaces\CRUDInterface;
use Exception;
use ReflectionException;
use App\Entities\Rol;
use App\Models\{
    RolModel,
    MasterModuleViewModel,
    ModuloVsSubmodulosModelView,
    GenericosVsSubmodulosModel,
    PermisosGenericosModel
};
use App\Libraries\SupportLogHandler;
class ModuleController extends BaseController 
{
    public function index()
    {
        $masterModuleModel= new MasterModuleViewModel();
        $masterModule=$masterModuleModel->findAll();
        return $this->getResponse([
            'message'      => 'OK',
            'items'        => $masterModule,
        ]);
    }
    public function getModules()
    {
        try{
            $vars=(Object)$this->request->getVar();
            if(!isset($vars->idRol) or empty($vars->idRol)){
                throw new Exception('No se envio el id del rol',1);
            }
            $MaestrosVsSubmodulosModel= new ModuloVsSubmodulosModelView();
            $genericosVsSubmodulosModel= new GenericosVsSubmodulosModel();
            $permisosGenericosModel= new PermisosGenericosModel();
            $permissions=$permisosGenericosModel->asArray()
                                                ->select('id as idGenerico, nombrePermiso as name')
                                                ->findAll();
            $modules=$MaestrosVsSubmodulosModel->findAll();
            $approvedPermissions=$genericosVsSubmodulosModel->asObject()->select('idSubmodulos as idSubmodule,idGenericos as idGenerico')
                                                            ->where('idRol',$vars->idRol)
                                                            ->findAll();
            $modules=$this->sortModules($modules);
            return $this->getResponse([
                'message'      => 'OK',
                'items'        => $modules,
                'permissions'  => $permissions,
                'approved'     => $approvedPermissions,
            ]);   

        }catch(Exception $e){
            if($e->getCode()==1){
                $this->log->info($e->getMessage());
                return $this->getResponse([
                    'message' => $e->getMessage(),
                ],HTTP_BAD_REQUEST);
            }
            $this->log->error($e->getMessage());
            return $this->getResponse([
                'message' => 'Error al obtener los modulos',
            ],HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function sortModules(array $modules): array
    {
        $modulesSorted = [];
        $idToIndexMap = [];
        foreach ($modules as $module) {
            $idModule = $module['idModulo'];
            if (isset($idToIndexMap[$idModule])) {
                continue;
            }
            $modulesSorted[] = [
                'id' => $idModule,
                'name' => $module['nombreModulo'],
                'children' => []
            ];
            $idToIndexMap[$idModule] = count($modulesSorted) - 1;
        }
        //add submodules
        foreach ($modules as $module) {
            $idModule      = $module['idModulo'];
            $idSubmodule   = $module['idSubmodulo'];
            $nameSubmodule = $module['nombreSubmodulo'];
            $index         = $idToIndexMap[$idModule];

            $modulesSorted[$index]['children'][] = [
                'id' => $idSubmodule,
                'name' => $nameSubmodule
            ];
        }

        return $modulesSorted;
    }
}
