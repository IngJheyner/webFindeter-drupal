<?php
namespace Drupal\findeter_pqrsd;

use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;


use Drupal\findeter_pqrsd\ApiSmfcAbs;
/**
 * Service used to create a client web service for the SMFC REST API
 * @see Drupal\findeter_pqrsd\ApiSmfcAbs
 * @param $credentials
 *  A fix that brings the login credentials and uri to connect to the API
 *   - user: user value chain
 *   - pass: pass password value chain
 *   - secret_key: secret key value chain
 *   - uri: Url https SMFC
 */

class ApiSmfc extends ApiSmfcAbs{

    protected $credentials;

    /**
     * @param $credentials
     * @param Drupal\Core\Http\ClientFactory $http_client_factory
     */

    public function __construct($credentials, ClientFactory $http_client_factory, LoggerChannelFactoryInterface $logger){

        $this->credentials = $credentials;        
        $this->secretKey = $this->credentials[0]["secret_key"];
        $this->uri = $this->credentials[0]["uri"];

        //Services
        $this->httpClientFactory = $http_client_factory;
        $this->logger = $logger;
        
        $this->login();
       
    }

    /**
     * @inheritDoc
     */
    public function login()
    {
        $user = $this->credentials[0]["user"];
        $pass = $this->credentials[0]["pass"]; 

        $data = '{"username": "'.$user.'", '.
                '"password": "'.$pass.'"}';        
        
        $signature = $this->signature($data);

        $tokens = $this->httpClient($signature, $data, 'login/');

       
    }
    
}