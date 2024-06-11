<?php

namespace App\Controllers;

use Exception;
use ReflectionException;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\{UserModel, RolModel, PersonModel, GenericosVsSubmodulosModel};
use App\Entities\{User, Rol, Person, Department};

class UserController extends BaseController
{
    protected $permidRol = [1, 2];
    public function index()
    {
        //
    }

    public function validateJWT()
    {
        $token = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$token) {
            return $this->getResponse(
                [
                    'message' => 'Token no recibido',
                ],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $apiUrl = getenv('app.baseUrl.tramiterscrc');

        $token = str_replace('Bearer ', '', $token);

        try {
            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => "$apiUrl/security/validarAutorizacion",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query(['token' => $token]),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/x-www-form-urlencoded',
                    ),
                )
            );

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);

                return $this->getResponse(
                    [
                        'message' => 'Error en la solicitud cURL',
                        'error' => $error_msg,
                    ],
                    ResponseInterface::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            curl_close($curl);

            if ($httpCode != 200 || !$response) {
                return $this->getResponse(
                    [
                        'message' => 'Token invalido',
                    ],
                    ResponseInterface::HTTP_UNAUTHORIZED
                );
            }

            $responseData = json_decode($response, true);

            if (!$responseData || !isset($responseData['idUsuario'])) {
                return $this->getResponse(
                    [
                        'message' => 'Token inválido',
                    ],
                    ResponseInterface::HTTP_UNAUTHORIZED
                );
            }

            return $this->getResponse(
                [
                    'message' => 'OK',
                    'user' => $responseData,
                    'token' => $token,
                ]

            );
        } catch (Exception $exception) {
            $this->log->error($exception->getMessage());
            return $this->getResponse(
                [
                    'status' => $exception->getCode(),
                    'error' => $exception->getMessage(),
                ],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
    }
    public function login()
    {
        $var = $this->request->getVar();
        try {
            $token = $var->token;

            $apiUrl = getenv('app.baseUrl.tramiterscrc');
            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => "$apiUrl/security/validarAutorizacion",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query(['token' => $token]),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/x-www-form-urlencoded',
                    ),
                )
            );

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);

                return $this->getResponse(
                    [
                        'message' => 'Error en la solicitud cURL',
                        'error' => $error_msg,
                    ],
                    ResponseInterface::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            curl_close($curl);

            if ($httpCode != 200 || !$response) {
                return $this->getResponse(
                    [
                        'message' => 'Token invalido',
                    ],
                    ResponseInterface::HTTP_UNAUTHORIZED
                );
            }

            $responseData = json_decode($response, true);

            if (!$responseData || !isset($responseData['idUsuario'])) {
                return $this->getResponse(
                    [
                        'message' => 'Token inválido',
                    ],
                    ResponseInterface::HTTP_UNAUTHORIZED
                );
            }

            return $this->getResponse(
                [
                    'message' => 'OK',
                    'user' => $responseData,
                    'token' => $token,
                ]
            );
        } catch (Exception $exception) {
            $this->log->error($exception->getMessage());
            return $this->getResponse(
                [
                    'status' => $exception->getCode(),
                    'error' => $exception->getMessage(),
                ],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
    }
}
