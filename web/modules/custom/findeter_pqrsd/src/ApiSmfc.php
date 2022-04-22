<?php
namespace Drupal\findeter_pqrsd;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Serialization\Json;
/**
 * @file
 * Service used to create a client web service for the SMFC REST API
 */

class ApiSmfc{

    protected $credentials;

    /**
     * @param $credentials
     * 
     */

    public function __construct($credentials)
    {

        $this->credentials = $credentials;

        $user = $this->credentials[0]["user"];
        $pass = $this->credentials[0]["pass"];
        $secret_key = $this->credentials[0]["secret_key"];

        $credentials = ["username" => "$user", "password" => "$pass"];

        $data = Json::encode($credentials);

        $signature = strtoupper(Crypt::hmacBase64($data, $secret_key));

        $hmac = hash_hmac('sha256', $data, $secret_key, FALSE);
        $hmacc = strtoupper(str_replace(['+', '/', '='], ['-', '_', ''], $hmac));
        
    }

    /**
     * 
     */
    private function signature(){
          
    
    }
    
}