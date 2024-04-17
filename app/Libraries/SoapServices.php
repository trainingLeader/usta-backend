<?php 

namespace App\Libraries;
use Exception;
use ReflectionException;
//use CodeIgniter\Log\Handlers\BaseHandler;
use CodeIgniter\Libraries;
class SoapServices
{
    public function saveDocument($params)
    {
        if(getenv('CI_ENVIRONMENT')=='development') {
            return [
                'Code' => '00',
                'DocumentHandle' => '3715253',
            ];
        }
        $xmlRequest = $this->generateXMLSaveDocument($params);
        try {
            $xmlRequest = urlencode($xmlRequest);
            $data= array(
                CURLOPT_URL => getenv('URL_SOAP_ONBASE'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'xmlCommand='.$xmlRequest,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            );
            $curl = curl_init();
            curl_setopt_array($curl, $data);
            $xml = curl_exec($curl);
            if(curl_errno($curl))
            {
                throw new Exception(curl_error($curl));
            }
            curl_close($curl);
            $xmlString = current(simplexml_load_string($xml));
            //pasar a object
            $xml = simplexml_load_string($xmlString);
            $json = json_encode($xml);
            $response =json_decode($json,TRUE);
            $response=[
                'Code' => $response['Response']['Code'],
                'DocumentHandle' => $response['Response']['Result']['DocumentHandle'],
            ];
            return $response;
        } catch (Exception $e) {
            return [
                'Code' => '100',
                'message' => $e->getMessage(),
            ];
        }
    }
    private function generateXMLSaveDocument($params)
    {
        // Aquí generamos el XML basado en los parámetros
        $xml = '<Request>';
        $xml .= '<Document>';
        $xml .= '<DiskGroupName>' . $params['DiskGroupName']. '</DiskGroupName>';
        $xml .= '<DocumentTypeName>' . $params['DocumentTypeName']. '</DocumentTypeName>';
        $xml .= '<FileExtension>pdf</FileExtension>';
        $xml .= '<FileFormat>PDF</FileFormat>';
        $xml .= '<Keywords>';
        foreach ($params['Keywords'] as $key => $value) {
            $xml .= '<Keyword name="' . $key . '">'.$value.'</Keyword>';
        }
        $xml .= '</Keywords>';
        $xml .= '<Pages>';
        $xml .= '<Page>'.$params['pdf'].'</Page>';
        $xml .= '</Pages>';
        $xml .= '</Document>';
        $xml .= '<Type>SaveDocument</Type>';
        $xml .= '</Request>';
        
        return $xml;
    }
}
?>