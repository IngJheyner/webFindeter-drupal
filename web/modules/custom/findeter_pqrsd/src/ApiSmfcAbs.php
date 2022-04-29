<?php
namespace Drupal\findeter_pqrsd;

use GuzzleHttp\Exception\RequestException;
use Drupal\Component\Serialization\Json;
/**
 * Abstract class with methods for creating and consuming the SMFC API
 */

abstract class ApiSmfcAbs{

    public $secretKey;
    public $uri;
    public $httpClientFactory;
    public $logger;

    /**
     * function to obtain the sending signature in the created request calls
     */
    public function signature($data){
        return strtoupper(hash_hmac('sha256',$data,$this->secretKey,FALSE));
    }

    /**
     * Login
     * Used to enter SMFC system and get access token
     */
    abstract public function login();

    /**
     * Http Client Guzzle
     * @param $signature
     * @param $data
     * @param $url
     */
    public function httpClient($signature, $data, $url){

        try {            
            /**
             * @var \GuzzleHttp\Client $client
             */
            $client = $this->httpClientFactory->fromOptions(['base_uri' => $this->uri]);
            
            switch ($url) {

                /* ===== ===== Login Acces Token y RefreshToken ===== ===== */
                case 'login/':

                    $response = $client->request('POST', $url, [
                        'headers' => [
                            'X-SFC-Signature' => $signature,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ],
                        'body' => $data,
                        ]);
                    
                    $motive = $response->getReasonPhrase();

                    $code = $response->getStatusCode();

                    if($response->getStatusCode())

                        $this->logger->get('API SMFC')->notice("Code: %code Mensaje: %message,se ha iniciado sesión en API SMFC exitosamente", ['%code' => $response->getStatusCode(), '%message' => $response->getReasonPhrase()]);

                        return Json::decode($response->getBody());

                break;
                
            }                      
      
        }catch (RequestException $e) {

            return $this->logger->get('API SMFC')->error("Código: %code\n\n Mensaje: %message", ['%code' => $e->getCode(), '%message' => $e->getMessage()]);

        }
    }
}