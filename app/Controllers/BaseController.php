<?php

namespace App\Controllers;

use Config\Services;
use CodeIgniter\Controller;
use Psr\Log\LoggerInterface;
use CodeIgniter\HTTP\CLIRequest;
use App\Libraries\SupportLogHandler;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Validation\Exceptions\ValidationException;
date_default_timezone_set('America/Bogota');
/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['isProduction'];
	protected $log;
    public function __construct()
    {
        $this->log   = new SupportLogHandler('support');
    }

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }
    /**
	 * This function returns a JSON response with a given HTTP status code and response body.
	 * 
	 * @param array responseBody An array containing the response data that will be returned in the
	 * response body.
	 * @param int code The HTTP status code to be set in the response. It is an optional parameter with a
	 * default value of 200 (HTTP_OK).
	 * 
	 * @return ResponseInterface an instance of a response object with a specified HTTP status code and a JSON-encoded
	 * response body.
	 */
	public function getResponse(array $responseBody, int $code = ResponseInterface::HTTP_OK): ResponseInterface
	{
		return $this->response
					->setStatusCode($code)
					->setJSON(json_encode($responseBody));
	}
	/**
	 * This function retrieves the input data from an incoming request in PHP, either from the POST data
	 * or from the request body if the former is empty.
	 * 
	 * @param IncomingRequest request  is an object of the IncomingRequest class, which represents
	 * an incoming HTTP request. It contains information about the request such as the HTTP method,
	 * headers, and body.
	 * 
	 * @return an array of input data obtained from the incoming request. If the request has a POST
	 * method, it returns the POST data. If the POST data is empty, it converts the body of the request
	 * into an associative array using JSON decoding and returns it.
	 */
	public function getRequestInput(IncomingRequest $request){
		$input = $request->getPost();
		if (empty($input)) {
			//convertir el cuerpo de la solicitud en una matriz asociativa
			$input = json_decode($request->getBody(), true);
		}
		return $input;
	}
	/**
	 * The function validates a given input based on a set of rules and messages, and returns the result.
	 * 
	 * @param input The input data that needs to be validated.
	 * @param array rules An array or string containing the validation rules to be applied to the input
	 * data. If it is a string, it should correspond to a group of rules defined in the Config\Validation
	 * file.
	 * @param array messages An optional array of custom error messages to be used for validation. If not
	 * provided, the default error messages defined in the validation configuration file will be used.
	 * 
	 * @return the result of running the validation rules on the input data. The result will be a boolean
	 * value indicating whether the input data passed all the validation rules or not.
	 */
	public function validateRequest($input, array $rules, array $messages =[])
	{
		$this->validator = Services::Validation()->setRules($rules);
		// Si reemplaza la matriz $ rules con el nombre del grupo
		if (is_string($rules)) {
			$validation = config('Validation');
	
			/* Si la regla no se encuentra en el \Config\Validation, se debe 
				lanzar una excepción para que el desarrollador pueda encontrarla. */
			if (!isset($validation->$rules)) {
				throw ValidationException::forRuleNotFound($rules);
			}
	
			/* Si no se define ningún mensaje de error, utilice el mensaje de error 
			en el archivo Config\Validation*/
			if (!$messages) {
				$errorName = $rules . '_errors';
				$messages = $validation->$errorName ?? [];
			}
	
			$rules = $validation->$rules;
		}
		$res=$this->validator->setRules($rules, $messages)->run($input);
		return $res;
	}
	public function createDirectory(string $path){
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}
