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

        $data = json_encode($credentials);

        $hmac = hash_hmac('sha256', utf8_encode($data), utf8_encode($secret_key),FALSE);

        $base =strtoupper($hmac);
        
        //$hmacc = strtoupper(str_replace(['+', '/', '='], ['-', '_', ''], $base_64));
        
    }
    /**
     * 
     */
    private function signature(){
          
    
    }
    
}