<?php
namespace Drupal\findeter_pqrsd\Api;

use GuzzleHttp\Exception\RequestException;
use Drupal\Component\Serialization\Json;
/**
 * Abstract class with methods for creating and consuming the SMFC API
 * @var $uri
 *  - URL base SMFC
 * @var Drupal\Core\Http\ClientFactory $httpClientFactory
 * @var Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
 * @var Drupal\Core\State\StateInterface $state
 */

class ApiSmfcHttp{
    
    protected $uri;

    /* Servicios ===== ===== */
    protected $httpClientFactory;
    protected $logger;
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
    public function httpClient($signature, $data, $endpoint): array{ 

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

                /* ===== ===== Crear Queja o Reclamo ===== ===== */
                case 'queja/':

                    $response = $client->request('POST', $endpoint, [
                        'headers' => [
                            'X-SFC-Signature' => $signature,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer '.$tokens['access'],
                        ],
                        'body' => $data,
                    ]);

                    if($response->getStatusCode() == '201'){

                        $dataResponse = Json::decode($response->getBody());

                        $this->logger->get('API SMFC')->notice("Code: %code Mensaje: %message, Se ha creado la queja o reclamo con radicado No. %settled como cliente web services en el sistema <strong> API SMFC.", ['%code' => $response->getStatusCode(), '%message' => $response->getReasonPhrase(), '%settled' => $dataResponse['codigo_queja']]);

                        return $dataResponse;

                    }

                break;
                
            }                      
      
        }catch (RequestException $e) {

            $this->logger->get('API SMFC')->error("Código: %code\n\n Mensaje: %message", ['%code' => $e->getCode(), '%message' => $e->getMessage()]);

            return [
                "code" => $e->getCode(),
                "mensseger" => $e->getMessage()
            ];

        }
    }
}