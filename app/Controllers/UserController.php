<?php

namespace App\Controllers;

use Exception;
use ReflectionException;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\{UserModel, RolModel, PersonModel,GenericosVsSubmodulosModel};
use App\Entities\{User, Rol, Person, Department};
class UserController extends BaseController
{
    protected $permidRol=[1,2];
    public function index()
    {
        //
    }
    public function validateJWT(){
        $token = $this->request->getServer('HTTP_AUTHORIZATION');
        if(!$token){
            return $this->getResponse(
                [
                    'message' => 'Token no recibido',
                ]
            );
        }
        $token=explode(' ', $token)[1];
        try {
            /* $session=getJWTData($token);
            $user = new User(['email'=>$session->email]);
            $user->getUserByEmail(); */
            $users =[
                [
                    'data' => [
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Super administrador",
                        'document'=> 1234567,
                        'rol'=> "Super administrador",
                        'email'=> "yuli.alvarez01@ustabuca.edu.co",
                    ],
                    'token'=>"12345superadmin",

                ],
                [
                    'data' => [
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Notificaciones Creador",
                        'document'=> 12345678,
                        'rol'=> "Notificaciones Creador",
                        'email'=> "madeleine.gil@crcom.gov.co",
                    ],
                    'token'=>'12345creador'
                ],
                [
                    'data' => [
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Agente",
                        'document'=> 123456,
                        'rol'=> "Agente",
                        'email'=> "chejidev@gmail.com",
                    ],
                    "token"=> "12345agente"
                ],
                [
                    'data'=>[
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Notificaciones Revisor",
                        'document'=> 12345,
                        'rol'=> "Notificaciones Revisor",
                        'email'=> "ana7@gmail.com",
                    ],
                    "token"=> "12345revisor"
                ],
                [
                    'data'=>[
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Notificaciones Aprobador",
                        'document'=> 1234,
                        'rol'=> "Notificaciones Aprobador",
                        'email'=> "Pedro7@gmail.com",
                    ],
                    "token"=> "12345aprobador",
                ],
                [
                    'data'=>[
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Notificaciones Creador",
                        'document'=> 123,
                        'rol'=> "Notificaciones Creador",
                        'email'=> "usta@usta.com",
                    ],
                    "token"=> '12345creador2',
                ],
                [
                    'data'=>[
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Configuración",
                        'document'=> 12,
                        'rol'=> "Configuración",
                        'email'=> "config@usta.com",
                    ],
                    "token"=> "12345configuracion",
                ],
            ];
            $genericosVsSubmodulosModel= new GenericosVsSubmodulosModel();
            $rolModel= new RolModel();
            //buscamos el usuario segun el token dado
            $user =array_filter($users, function($user) use ($token) {
                return $user['token'] == $token;
            });
            if(!$user){
                throw new Exception('Token no valido', EXIT_ERROR);
            }
            $user=(object)current($user);
            $user->data=(object)$user->data;
            $rol=$rolModel->where('nombre',$user->data->rol)
                          ->first();
            if(!$rol){
                throw new Exception('Rol no encontrado', EXIT_ERROR);
            }
            $user->data->idRol=$rol->id;
            $approvedPermissions=$genericosVsSubmodulosModel->asObject()
                                                            ->select('idGenericos as idGenerico,idSubmodulos as idSubmodule')
                                                            ->where('idRol',$rol->id)
                                                            ->findAll();
            $user->data->permissions=$approvedPermissions;
            return $this->getResponse(
                [
                    'message' => 'OK',
                    'user'    => $user->data,
                    'token'   => $user->token,
                ]
            );
        } catch (Exception $exception) {
            $this->log->error($exception->getMessage());
            return $this->getResponse(
                [
                    'status'=>$exception->getCode(),
                    'error' => $exception->getMessage(),
                ],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
    } 
    public function login(){
        $var= $this->request->getVar();
        try {
            $users =[
                [
                    'data' => [
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Super administrador",
                        'document'=> 1234567,
                        'rol'=> "Super administrador",
                        'email'=> "yuli.alvarez01@ustabuca.edu.co",
                    ],
                    'password'=>"123456",
                    'token'=>"12345superadmin",

                ],
                [
                    'data' => [
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Notificaciones Creador",
                        'document'=> 12345678,
                        'rol'=> "Notificaciones Creador",
                        'email'=> "madeleine.gil@crcom.gov.co",
                    ],
                    'password'=>'123456',
                    'token'=>'12345creador'
                ],
                [
                    'data' => [
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Agente",
                        'document'=> 123456,
                        'rol'=> "Agente",
                        'email'=> "chejidev@gmail.com",
                    ],
                    "password"=> "123456",
                    "token"=> "12345agente"
                ],
                [
                    'data'=>[
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Notificaciones Revisor",
                        'document'=> 12345,
                        'rol'=> "Notificaciones Revisor",
                        'email'=> "ana7@gmail.com",
                    ],
                    "password"=> "123456",
                    "token"=> "12345revisor"
                ],
                [
                    'data'=>[
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Notificaciones Aprobador",
                        'document'=> 1234,
                        'rol'=> "Notificaciones Aprobador",
                        'email'=> "Pedro7@gmail.com",
                    ],
                    "password"=> "123456",
                    "token"=> "12345aprobador",
                ],
                [
                    'data'=>[
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Notificaciones Creador",
                        'document'=> 123,
                        'rol'=> "Notificaciones Creador",
                        'email'=> "usta@usta.com",
                    ],
                    "password"=> '123456',
                    "token"=> '12345creador2',
                ],
                [
                    'data'=>[
                        'documentType'=> "1",
                        'idPerson'=> 1,
                        'fullName'=> "Configuración",
                        'document'=> 12,
                        'rol'=> "Configuración",
                        'email'=> "config@usta.com",
                    ],
                    "password"=> "123456",
                    "token"=> "12345configuracion",
                ],
            ];
            $genericosVsSubmodulosModel= new GenericosVsSubmodulosModel();
            $rolModel= new RolModel();
            //buscamos el usuario segun el token dado
            $user =array_filter($users, function($user) use ($var) {
                return     $user['data']['documentType'] == $var->documentType 
                        && $user['data']['document'] == $var->document 
                        && $user['password'] == $var->password;
            });
            if(!$user){
                throw new Exception('Token no valido', EXIT_ERROR);
            }
            $user=(object)current($user);
            $user->data=(object)$user->data;
            $rol=$rolModel->where('nombre',$user->data->rol)
                          ->first();
            if(!$rol){
                throw new Exception('Rol no encontrado', EXIT_ERROR);
            }
            $user->data->idRol=$rol->id;
            $approvedPermissions=$genericosVsSubmodulosModel->asObject()
                                                            ->select('idGenericos as idGenerico,idSubmodulos as idSubmodule')
                                                            ->where('idRol',$rol->id)
                                                            ->findAll();
            $user->data->permissions=$approvedPermissions;
            return $this->getResponse(
                [
                    'message' => 'OK',
                    'user'    => $user->data,
                    'token'   => $user->token,
                ]
            );
        } catch (Exception $exception) {
            $this->log->error($exception->getMessage());
            return $this->getResponse(
                [
                    'status'=>$exception->getCode(),
                    'error' => $exception->getMessage(),
                ],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
    } 
}
