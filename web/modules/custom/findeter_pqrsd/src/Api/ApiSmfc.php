<?php
namespace Drupal\findeter_pqrsd\Api;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\findeter_pqrsd\Api\ApiSmfcHttp;
use Drupal\findeter_pqrsd\Api\ApiSmfcInterface;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Service used to create a client web service for the SMFC REST API
 * @author 3ddesarrollo
 * @see Drupal\findeter_pqrsd\Api\ApiSmfcHttp
 * @see Drupal\findeter_pqrsd\Api\ApiSmfcInterface
 * @param $credentials
 *  A fix that brings the login credentials and uri to connect to the API
 *   - user: user value chain
 *   - pass: pass password value chain
 *   - secret_key: secret key value chain
 *   - uri: Url https SMFC
 *   - num_code_entity: Numero de entidad que pertenece a la SMFC.
 * @param Drupal\Core\Http\ClientFactory $http_client_factory
 * @param Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
 * @param Drupal\Core\State\StateInterface $state
 */

class ApiSmfc extends ApiSmfcHttp implements ApiSmfcInterface{

    /**
     * Credenciales y valores para acceder a la API SMFC.
     *@see Drupal\findeter_pqrsd\findeter_pqrsd.services
     */
    private $credentials;
    private $secretKey;
    protected $tipEntity;
    protected $codeEntity;

    //Servicios

    /**
     * @var use Drupal\Core\Http\ClientFactory
     *  - Propiedad que hereda desde
     * @see Drupal\findeter_pqrsd\Api\ApiSmfcHttp $httpClientFactory;
     */

    /**
     * @var Drupal\Core\Logger\LoggerChannelFactoryInterface
     * - Propiedad que hereda desde
     * @see Drupal\findeter_pqrsd\Api\ApiSmfcHttp $logger;
     */

    /**
     * @var Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;
    /**
    * @var Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
    */
    protected $dateFormatter;
    /**
     * @var Drupal\Core\Messenger\MessengerInterface $messenger
     */
    protected $messenger;

    public function __construct($credentials, ClientFactory $http_client_factory, LoggerChannelFactoryInterface $logger,
    StateInterface $state, EntityTypeManagerInterface $entity_type_manager, DateFormatterInterface $date_formatter, MessengerInterface $messenger){

        /* ===== ===== Credenciales de acceso ===== ===== */
        $this->credentials = $credentials;   

        $this->secretKey = $this->credentials[0]["secret_key"];
        $this->uri = $this->credentials[0]["uri"];
        $this->tipEntity = $this->credentials[0]['tip_entity'];
        $this->codeEntity = $this->credentials[0]['code_entity'];

        /* ===== ===== Servicios ===== ===== */
        $this->httpClientFactory = $http_client_factory;
        $this->logger = $logger;
        $this->state = $state;
        $this->entityTypeManager = $entity_type_manager;
        $this->dateFormatter = $date_formatter;
        $this->messenger = $messenger;

        //$this->login();
        //$this->refreshToken();
        //$this->postComplaints(262);
       
    }

    /**
     *@inheritDoc
     */
    public function getTipCodeEntity(string $num_settled): string{
        return $this->tipEntity.$this->codeEntity.$num_settled;
    }

    /**
     * @inheritDoc
     */
    public function getExtFile(): string{

        return 'pdf jpg png mp4 docx doc xls bmp mp3 msg jpeg xlsx zip';
    }

    /**
     * @inheritDoc
     */
    public function login(): bool
    {
        //Se pasan las credencias de usuario y contrasenia.
        $user = $this->credentials[0]["user"];
        $pass = $this->credentials[0]["pass"]; 

        /* Se crea una variable de tipo string fomentado a uno de tipo objeto. ===== ===== */
        $data = '{"username": "'.$user.'", '.
                '"password": "'.$pass.'"}';        
        
        /* Firma encrypt sha256 en funcion de hmac.===== ===== */
        $signature = strtoupper(hash_hmac('sha256',$data,$this->secretKey,FALSE));

        /* Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== */
        $response = $this->httpClient($signature, $data, 'login/');

        /* Se obtiene la fecha y hora actual ===== ===== */
        $response["time"] = strtotime(new DrupalDateTime());

        /* Se guarda el token como una variable de estado en el sistema. ==== ====== */
        $this->state->set('findeter_pqrsd.api_smfc_token', $response);
        
        if(isset($response['access']))
            return true;
        else
            return false;
    }

    /**
     * @inheritDoc
     */
    /*public function refreshToken(): void
    {*/
        /*Informacion de los tokens que se guardan como variables de estado. ===== ===== */
        //$arrayTokens = $this->state->get('findeter_pqrsd.api_smfc_token');
        
        //$token = $arrayTokens['refresh'] ?: $arrayTokens['code'] ;

        /*if(!is_numeric($token)){

            /* Se crea una variable de tipo string fomentado a uno de tipo objeto. ===== ===== */
            //$data = '{"refresh": "'.$token.'"}';

            /* Firma encrypt sha256 en funcion de hmac.===== ===== */
            //$signature = strtoupper(hash_hmac('sha256',$data,$this->secretKey,FALSE));

            /* Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== */
            //$response = $this->httpClient($signature, $data, 'token/refresh');

            /* Se obtiene la fecha y hora actual ===== ===== */
            //$response["time"] = strtotime(new DrupalDateTime()); 

            /* Se guarda el token como una variable de estado en el sistema. ==== ====== */
            //$this->state->set('findeter_pqrsd.api_smfc_token', $response);

        /*}        
    }*/

    /**
     * @inheritDoc
     */
    public function postComplaints(int $nid): void{

        //Carga de informacion del nodo por medio de su $nid
        $nodeStorage = $this->entityTypeManager->getStorage('node')->load($nid);

        //Carga de informacion taxonomia
        $termStorage = $this->entityTypeManager->getStorage('taxonomy_term');

        //Codigo de Radicado(Queja o Reclamos) ===== ===== 
        $codComplaints = $nodeStorage->get("field_pqrsd_numero_radicado")->getValue()[0]['value'];

        //Codigo de Depto Dane ===== =====
        $deptTargetId = $nodeStorage->get("field_pqrsd_departamento")->getValue()[0]['target_id'];

        $deptValue = $termStorage->load($deptTargetId);

        $deptCodeDane = $deptValue->get("field_code_dane_dpto")->getValue()[0]['value'];

        //Codigo Mpio. Dane ===== ===== 
        $mpioTargetId = $nodeStorage->get('field_pqrsd_municipio')->getValue()[0]['target_id'];

        $mpioValue = $termStorage->load($mpioTargetId);

        $mpioCodeDane = $mpioValue->get("field_code_dane_dpto")->getValue()[0]['value'];

        //Producto ===== =====
        $codProduct = $nodeStorage->get("field_pqrsd_nombre_producto")->getValue()[0]['value'];

        //Motivo
        $codMotive = $nodeStorage->get("field_pqrsd_motivo")->getValue()[0]['value'];

        //Fecha de creacion ===== ===== */
        $createdTimeStamp = $nodeStorage->get("created")->getValue()[0]['value'];

        $created =  str_replace(" ", "", ($this->dateFormatter->format($createdTimeStamp, 'custom', 'Y-m-d \T\ H:i:s')));

        // Nombres y Apellidos ===== =====
        $names = $nodeStorage->get("field_pqrsd_primer_nombre")->getValue()[0]['value'];

        if(isset($nodeStorage->get("field_pqrsd_segundo_nombre")->getValue()[0]['value']))
            $names .= " ".$nodeStorage->get("field_pqrsd_segundo_nombre")->getValue()[0]['value'];
        
        $names .= " ".$nodeStorage->get("field_pqrsd_primer_apellido")->getValue()[0]['value'];
        
        if(isset($nodeStorage->get("field_pqrsd_segundo_apellido")->getValue()[0]['value']))
            $names .= " ".$nodeStorage->get("field_pqrsd_segundo_apellido")->getValue()[0]['value'];
        
        //Tipo de solicitante o persona field_pqrsd_tipo_solicitante
        $typApplicant = $nodeStorage->get("field_pqrsd_tipo_solicitante")->getValue()[0]['value'];
        
        //Tipo de documento si es persona juridica por defecto es 4
        if($typApplicant == 2){
            $typDocument = 3;
            $numDocument = $nodeStorage->get("field_pqrsd_nit")->getValue()[0]['value'];
        }else{
            $typDocument = $nodeStorage->get("field_pqrsd_tipo_documento")->getValue()[0]['value'];
            $numDocument = $nodeStorage->get("field_pqrsd_numero_id")->getValue()[0]['value'];
        }

        //Instancia de recepcion
        $instanceRecep = $nodeStorage->get("field_pqrsd_instance_reception")->getValue()[0]['value'];

        //Punto de recepcion o forma de recepcion
        $formRecep = $nodeStorage->get("field_pqrsd_forma_recepcion")->getValue()[0]['value'];

        if($formRecep == "electronico")
            $formRecep = 1;
            
        //Descripcion de solicitud field_pqrsd_descripcion
        $DescriptionSolic = $nodeStorage->get("field_pqrsd_descripcion")->getValue()[0]['value'];

        //Anexo archivos para la queja
        $anexFileComplaintsFile = $nodeStorage->get("field_pqrsd_archivo")->getValue();

        if(sizeof($anexFileComplaintsFile))
            $anexFileComplaints = "true";
        else
            $anexFileComplaints = "false";

        $data = '{"tipo_entidad": "'.$this->tipEntity.'", '.
            '"entidad_cod": "'.$this->codeEntity.'", '.
            '"codigo_queja": "'.$codComplaints.'", '.
            '"codigo_pais": "170", '.
            '"departamento_cod": "'.$deptCodeDane.'", '.
            '"municipio_cod": "'.$mpioCodeDane.'", '.
            '"canal_cod": null, '.
            '"producto_cod": "'.$codProduct.'", '.
            '"macro_motivo_cod": "'.$codMotive.'", '.
            '"fecha_creacion": "'.$created.'", '.
            '"nombres": "'.strtoupper($names).'", '.
            '"tipo_id_CF": "'.$typDocument.'", '.
            '"numero_id_CF": "'.$numDocument.'", '.
            '"tipo_persona": "'.$typApplicant.'", '.
            '"insta_recepcion": "'.$instanceRecep.'", '.
            '"punto_recepcion": "'.$formRecep.'", '.
            '"admision": "9", '.
            '"texto_queja": "'.$DescriptionSolic.'", '.
            '"anexo_queja": "'.$anexFileComplaints.'", '.
            '"ente_control": null}';

        //Firma encrypt sha256 en funcion de hmac.===== ===== 
        $signature = strtoupper(hash_hmac('sha256',$data,$this->secretKey,FALSE));

        //Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== 
        $response = $this->httpClient($signature, $data, 'queja/');

        if(isset($response['code'])){

            $this->logger->get('API SMFC')->warning("Code: %code Mensaje crear radicado: %message <br> Se ha producido un error al crear radicado No. %settled como cliente web services en el sistema <strong> API SMFC.", ['%code' => $response['code'], '%message' => $response['message'], '%settled' => $codComplaints]);

        }else{

            if($anexFileComplaints == "true"){

                $file_storage = $this->entityTypeManager->getStorage('file');

                foreach ($anexFileComplaintsFile as $file) {

                    if(sizeof($file)){
                        $anexFile = $file_storage->load($file['target_id']);

                        /* Se crea una variable de tipo string fomentado a uno de tipo objeto. ===== ===== */
                        $dataSignature = '{"type": "'.$anexFile->getMimeType().'", '.
                        '"codigo_queja": "'.$codComplaints.'"}';

                        //Firma encrypt sha256 en funcion de hmac.===== ===== 
                        $signature = strtoupper(hash_hmac('sha256',$dataSignature,$this->secretKey,FALSE));

                        $data = ['file' => $anexFile->getFileUri(), 'type' => $anexFile->getMimeType(), 'code' => $codComplaints];

                        //Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== 
                        $response = $this->httpClient($signature, $data, 'storage/');

                        if(isset($response['code'])){

                            $this->logger->get('API SMFC')->warning("Code: %code Mensaje anexo radicado: %message <br> Se ha producido un error al crear el anexo del radicado No. %settled como cliente web services en el sistema <strong> API SMFC.", ['%code' => $response['code'], '%message' => $response['message'], '%settled' => $codComplaints]);

                        }

                    }

                }

            }
        }

    }
    
}