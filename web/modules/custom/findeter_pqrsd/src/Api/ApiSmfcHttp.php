<?php
namespace Drupal\findeter_pqrsd\Api;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use Drupal\Component\Serialization\Json;

class ApiSmfcHttp{

    /**
     * Abstract class with methods for creating and consuming the SMFC API
     * @var $uri
     *  - URL base SMFC
     */
    protected $uri;

    /* Servicios ===== ===== */
    /**
    * @var Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
    */
    protected $logger;
    /**
    * @var Drupal\Core\Http\ClientFactory $httpClientFactory
    */
    protected $httpClientFactory;
    /**
    * @var Drupal\Core\State\StateInterface $state
    */
    protected $state;

    /**
     * Http Client Guzzle
     * @param $signature
     *  - firma
     * @param $data
     *  - contenido
     * @param $endpoint
     *  - /trazo url
     * @return array $response
     */
    public function httpClient($signature, $data, $endpoint, $method = NULL): array{ 

        try {            
            /**
             * @var \GuzzleHttp\Client $client
             */
            $client = $this->httpClientFactory->fromOptions(['base_uri' => $this->uri]);

            /* Informacion de los tokens que se guardan como variables de estado ===== ===== */
            $tokens = $this->state->get('findeter_pqrsd.api_smfc_token');
            
            switch ($endpoint) {

                /* ===== ===== Login Acces Token ===== ===== */
                case 'login/':

                    $response = $client->request('POST', $endpoint, [
                        'headers' => [
                            'X-SFC-Signature' => $signature,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ],
                        'body' => $data,
                    ]);

                    if($response->getStatusCode() == '200'){

                        return Json::decode($response->getBody());

                    }

                break;

                /* ===== ===== RefreshToken ===== ===== */
                /*case 'token/refresh':

                    $response = $client->request('POST', $endpoint, [
                        'headers' => [
                            'X-SFC-Signature' => $signature,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer '.$tokens['access'],
                        ],
                        'body' => $data,
                    ]);

                    if($response->getStatusCode() == '200'){

                        $this->logger->get('API SMFC')->notice("Code: %code Mensaje: %message, Se ha actualizado las variables Access Token del sistem SMFC.", ['%code' => $response->getStatusCode(), '%message' => $response->getReasonPhrase()]);

                        return Json::decode($response->getBody());

                    }

                break;*/

                /* ===== ===== Crear o actualizar Queja o Reclamo ===== ===== */
                case 'queja/':

                    $method ??= 'POST';//Se valida el metodo de envio
                    $param = isset($data['code']) ? $data['code'].'/' : '';//Si viene algun parametro para actualizacion del radicado

                    $response = $client->request($method, $endpoint.$param, [
                        'headers' => [
                            'X-SFC-Signature' => $signature,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer '.$tokens['access'],
                        ],
                        'body' => $data['data'] ?? $data,
                    ]);

                    if($response->getStatusCode() == '201'){

                        $dataResponse = Json::decode($response->getBody());

                        $this->logger->get('API SMFC')->info("Code: %code Mensaje: %message, Se ha creado la queja o reclamo con radicado No. %settled como cliente web services en el sistema <strong> API SMFC.", ['%code' => $response->getStatusCode(), '%message' => $response->getReasonPhrase(), '%settled' => $dataResponse['codigo_queja']]);

                        return $dataResponse;

                    }else if($response->getStatusCode() == '200'){

                        $dataResponse = Json::decode($response->getBody());

                        $this->logger->get('API SMFC')->info("Code: %code Mensaje: %message, Se ha actualizado la queja o reclamo con radicado No. %settled como cliente web services en el sistema <strong> API SMFC.", ['%code' => $response->getStatusCode(), '%message' => $response->getReasonPhrase(), '%settled' => $dataResponse['codigo_queja']]);

                        return $dataResponse;
                    }

                break;

                /* ===== ===== Crear y adjuntar archivos de Queja ===== ===== */
                case 'storage/':

                    $multipart = [
                        [
                            'name' => 'file',
                            'contents' => fopen($data['file'], 'r'),
                        ],
                        [
                            'name' => 'type',
                            'contents' => $data['type'],
                        ],
                        [
                            'name' => 'codigo_queja',
                            'contents' => $data['code'],
                        ],
                    ];

                    $boundary = new MultipartStream($multipart);

                    $response = $client->request('POST', $endpoint, [
                        'headers' => [
                            'X-SFC-Signature' => $signature,
                            'Content-Type' => 'multipart/form-data; boundary='.$boundary->getBoundary(),
                            'Authorization' => 'Bearer '.$tokens['access'],
                        ],
                        'body' => new MultipartStream($multipart, $boundary),            
                    ]);

                    if($response->getStatusCode() == '201'){

                        $dataResponse = Json::decode($response->getBody());

                        $this->logger->get('API SMFC')->info("Code: %code Mensaje: %message, Se ha creado el anexo con ID %id, radicado No. %settled cliente web services en el sistema <strong> API SMFC.", ['%code' => $response->getStatusCode(), '%message' => $response->getReasonPhrase(), '%settled' => $dataResponse['codigo_queja'], '%id' => $dataResponse['id']]);

                        return $dataResponse;

                    }

                break;
                
            }                      
      
        }catch (RequestException $e) {

            $this->logger->get('API SMFC')->error("Código: %code Mensaje: %message", ['%code' => $e->getCode(), '%message' => $e->getResponse()->getBody()->getContents()]);

            return [
                "code" => $e->getCode(),
                "message" => $e->getResponse()->getBody()->getContents()
            ];

        }
    }
}