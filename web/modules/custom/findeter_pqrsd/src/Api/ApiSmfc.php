<?php
namespace Drupal\findeter_pqrsd\Api;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\findeter_pqrsd\Api\ApiSmfcHttp;
use Drupal\findeter_pqrsd\Api\ApiSmfcInterface;
use Drupal\file\FileRepositoryInterface;

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
 * @param ClientFactory $http_client_factory
 * @param LoggerChannelFactoryInterface $logger
 * @param StateInterface $state
 * @param EntityTypeManagerInterface $entity_type_manager
 * @param DateFormatterInterface $date_formatter
 * @param FileSystemInterface $file_system
 * @param FileRepositoryInterface $file_repository
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
     * @var Drupal\Core\File\FileSystemInterface $fileSystem
     */
    protected $fileSystem;

    /**
     * @var Drupal\file\FileRepositoryInterface $fileRepository
     */
    protected $fileRepository;

    public function __construct($credentials, ClientFactory $http_client_factory, LoggerChannelFactoryInterface $logger,
    StateInterface $state, EntityTypeManagerInterface $entity_type_manager, DateFormatterInterface $date_formatter, FileSystemInterface $file_system, FileRepositoryInterface $file_repository){

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
        $this->fileSystem = $file_system;
        $this->fileRepository = $file_repository;

        //$this->login();
        //$this->refreshToken();
        //$this->postComplaints(277);
        //$this->putComplaints(278);
       
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
    public function login(): bool{
        //Se pasan las credencias de usuario y contrasenia.
        $user = $this->credentials[0]["user"];
        $pass = $this->credentials[0]["pass"]; 

        /* Se crea una variable de tipo string fomentado a uno de tipo objeto. ===== ===== */
        $data = '{"username": "'.$user.'", '.
                '"password": "'.$pass.'"}';        
        
        /* Firma encrypt sha256 en funcion de hmac.===== ===== */
        $signature = strtoupper(hash_hmac('sha256',$data,$this->secretKey,FALSE));

        /* Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== */
        $response = $this->httpClient($signature, 'POST', 'login/', $data);

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
    public function getComplaints($context){

        if($this->login()){

            //Variable que permite la validacion del flujo de importacion en cualquier momento del ciclo.
            $break = false;

            //Firma encrypt sha256 en funcion de hmac.===== ===== 
            $signature = strtoupper(hash_hmac('sha256',$this->uri.'queja/',$this->secretKey,FALSE));

            //Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== 
            $response = $this->httpClient($signature, 'GET', 'queja/');

            if(isset($response['code'])){

                $context['finished'] = 1;
                $context['message'] = t('Import failed.');
                $context['results']['error']['error_get'] = t('<p><strong>Import failed.</strong><br><br>There was an error in the import of complaints from the SuperIntedencia Financiera de Colombia, for more information contact the system administrator<p>');//Se presento algún error en la importación de quejas desde la Superintendencia Financiera de Colombia,

            }else{

                if($response['count'] != 0){

                    //Inicializamos valores
                    if (!isset($context['sandbox']['progress'])) {
                        $context['sandbox']['progress'] = 0;
                        //$context['sandbox']['current_node'] = 0;
                        $context['sandbox']['max'] = $response['count'];
                    }

                    //Iteramos el numero de paginas
                    for ($i=0; $i < $response['pages']; $i++) {

                        //Iteramos por cada radicado que se haya importado
                        foreach ($response['results'] as $key => $value) {

                            //Se pregunta si existe algun anexo adjunto al radicado
                            if($value['anexo_queja']){

                                //Firma encrypt sha256 en funcion de hmac.===== ===== 
                                $signature = strtoupper(hash_hmac('sha256',$this->uri.'storage/?codigo_queja__codigo_queja='.$value['codigo_queja'],$this->secretKey,FALSE));

                                //Cambiamos el nuevo enpoint par consultar y traer los archivos adjuntos
                                $data = ['newEnpoint' => "?codigo_queja__codigo_queja=".$value['codigo_queja']];

                                //Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== 
                                $responseFile = $this->httpClient($signature, 'GET', 'storage/', $data);
                                
                                if(isset($responseFile['code'])){

                                    $context['finished'] = 1;
                                    $context['message'] = t('File import error.');
                                    $context['results']['error']['error_get'] = t('<p><strong>File import error.</strong><br><br>There was an error in the import of complaints in the file section from the SuperFinanciera de Colombia, for more information contact the system administrator<p>');//Se presento algún error en la importación de quejas desde la Superintendencia Financiera de Colombia,
                                    $break = true;
                    
                                }else{
                                    //A veces, el anexo_queja es true y trae archivos vacios o nulos; hacemos una validacion para no entrar en la iteraccion
                                    if($responseFile['count'] != 0){

                                        //Iteramos los resultados de los archivos
                                        foreach ($responseFile['results'] as $keyF => $files) {

                                            //Los siguientes datos, como la fecha, hora y documentos se tratan para construir la ruta donde va ir guardado el archivo, este se guarda como archivo gestionado TEMPORAL.
                                            $date = $this->dateFormatter->format(strtotime(new DrupalDateTime()), 'custom', 'Y-m-d');
                                            $hour = $this->dateFormatter->format(strtotime(new DrupalDateTime()), 'custom', 'H');

                                            $numDoc = $value['tipo_id_CF'] == 3 ? 'NIT'.$value['numero_id_CF'] : $value['numero_id_CF'];

                                            //Directorio y ruta final donde va ir el archivo
                                            $directory = "private://pqrsd/Quejas/$date/T$hour"."---$numDoc";

                                            //Se obtiene la traza del nombre del archivo con parametros de la uri
                                            $basenameUri = $this->fileSystem->basename($files['file']);

                                            //Se divide en partes el basename para obtener por completo el nombre del archivo y su extension
                                            $nameFile = explode('?', $basenameUri);

                                            //Se crea el directorio donde va ir alojado los archivos
                                            $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);

                                            //Se crea el archivo como una nueva entidad 
                                            $fileEntity=$this->fileRepository->writedata(file_get_contents($files['file']), $directory.'/'.$nameFile[0], FileSystemInterface::EXISTS_RENAME);

                                            //Se extrae el nuevo identificador del archivo
                                            $fid = $fileEntity->id();

                                            //Se agrega un nuevo indice a $value con los fid del nuevo archivo agregado al sitio
                                            $value['archivos_fid'][$keyF] = $fid;
                                        }
                                        
                                    }
                                }
                            }

                            if($break)
                                break;
                            //Se agregan los valores a la variable resultado para ser mostrados.
                            $context['results'][] = $value;

                            //Actualizar el progreso de informacion.
                            $context['sandbox']['progress']++;
                            //$context['message'] = t('API SMFC: Importando @num quejas para findeter', ['@num' => count($context['results'])]);
                            
                        }

                        if($break)
                            break;

                        if(!is_null($response['next'])){

                            //Firma encrypt sha256 en funcion de hmac.===== ===== 
                            $signature = strtoupper(hash_hmac('sha256',$response['next'],$this->secretKey,FALSE));

                            //Nuevo enpoint
                            $data = ['newEndpoint' => $response['next']];

                            //Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== 
                            $response = $this->httpClient($signature, 'GET', 'queja/', $data);

                            if(isset($response['code'])){

                                $context['finished'] = 1;
                                $context['message'] = t('Import failed next.');
                                $context['results']['error']['error_get'] = t('<p><strong>Import failed next.</strong><br><br>There was an error in the import of complaints from the SuperIntedencia Financiera de Colombia, for more information contact the system administrator<p>');//Se presento algún error en la importación de quejas desde la Superintendencia Financiera de Colombia,
                                $break = true;
                            }

                        }
                        
                        if($break)
                            break;

                        $context['message'] = t('<strong>SuperIntendencia Financiera De Colombia:</strong> Importando @num quejas para findeter', ['@num' => count($context['results'])]);

                    }

                    //Si ocurre algun error de importacion en cualquier momento del ciclo, se debe eliminar la entidades de archivo creada como el mismo en su ruta.
                    if($break){

                        $fileStorage = $this->entityTypeManager->getStorage('file');
                        foreach ($context['results'] as $keyR => $result) {

                            if(!is_string($keyR)){

                                if(isset($result['archivos_fid'])){

                                    foreach ($result['archivos_fid'] as $key => $value) {
                                        //Datos de la entidad del archivo
                                        $file = $fileStorage->load($value);
                                        $file->setTemporary();//Se pasa a temporal en file.managed par su posterior eliminacion en bd
                                        $path = $file->getFileUri();//Path uri de ruta del archivo
                                        $file->save();

                                        //Eliminamos el archivo del servidor
                                        $this->fileSystem->delete($path);
                                    }
                                }
                            }
                        }
                    }else{
                        //Creamos una variable de estado para tener la data importada y poderla registrar como pqrsd en el sistema.
                        $this->state->set('findeter_pqrsd.api_smfc_data', $context['results']);
                    }

                    // Informar al motor por lotes que no hemos terminado,
                    // y proporcionar una estimación del nivel de finalización que alcanzamos.
                    if ($context['sandbox']['progress'] != $context['sandbox']['max'] && !$break) {
                        $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
                    }

                }else{
                    
                    for($i=0; $i<=15; $i++){

                        $context['finished'] = $i / 15;
                        $context['results']['empty'] = t('No results were found to import in the SMFC API.');
                        $context['message'] = t('<strong>Findeter: </strong>Querying the SMFC API...');
                    }

                }

            }

        }else{

            $context['finished'] = 1;
            $context['message'] = t('Connection error.');
            $context['results']['error']['error_get'] = t('<p><strong>Connection error</strong><br><br>An error occurred when trying to connect with the SMFC API, for more information contact the system administrator<p>');//Se presentó un error al tratar de conectarse con la API SMFC, para mayor información ponerse en contacto con el administrador del sistema
        }        
    }

     /**
     * @inheritDoc
     */
    public function ackComplaints($context){

        //$context['error']['error_get'] = 'error';
        //$this->login();

        if(!isset($context['results']['error']['error_get']) && !isset($context['results']['empty'])){

            //Inicializamos valores
            $dataState = $this->state->get('findeter_pqrsd.api_smfc_data');
            $i = 0;

            if (!isset($context['sandbox']['progress'])) {
                $context['sandbox']['progress'] = 0;
                //$context['sandbox']['current_node'] = 0;
                $context['results'] = [];
                $context['sandbox']['max'] = count($dataState);
            }

            $data = '{'.
                '"pqrs": [';
                    while ($i < count($dataState)){
                        
                        //Actualizar el progreso de informacion.
                        $context['sandbox']['progress']++;
                        //Mostramos el mensaje
                        $context['message'] = t('<strong>Findeter:</strong> Sending @num complaints through the ACK function to the SuperIntendencia Financiera De Colombia', ['@num' => count($context['results']) + 1]);

                        if(($i + 1) != count($dataState))
                            $data .= '"'.$dataState[$i]['codigo_queja'].'", ';
                        else
                            $data .= '"'.$dataState[$i]['codigo_queja'].'"';
                        //Se agregan los valores a la variable resultado para ser mostrados.
                        $context['results'][] = $i;
                        $i++;
                       
                        //$context['message'] = t('ACK enviando data: @num', ['@num' => count($context['results'])]);
                        
                    }
            $data .= ']}';

            //Firma encrypt sha256 en funcion de hmac.===== ===== 
            $signature = strtoupper(hash_hmac('sha256',$data,$this->secretKey,FALSE));

            //Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== 
            $response = $this->httpClient($signature, 'POST', 'complaint/ack', $data);

            if(isset($response['code']) && $response['code'] != '504'){

                $context['finished'] = 1;
                $context['message'] = t('ACK failed process 2.');
                $context['results']['error']['error_ack']['error'] = t('<p><strong>Import failed ACK.</strong><br><br>An error has occurred in the sending of data received through the ACK function, for more information contact the system administrator<p>');//Ha ocurrido en error en el envio de datos rebidos mediante la funcion ACK, para más información contacte al administrador del sistema
                $this->state->set('findeter_pqrsd.api_smfc_data', null);

            }else{

                // Informar al motor por lotes que no hemos terminado,
                // y proporcionar una estimación del nivel de finalización que alcanzamos.
                $context['message'] = t('ACK process.');
                if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
                    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
                }
            }            

        }else{

            if(isset($context['results']['empty'])){

                $context['message'] = t('<strong>Superintendencia Financiera de Colombia: </strong>No results found.');                

            }else{
                
                $context['message'] = t('Interrupting process 2.');
                $context['results']['error']['error_ack']['warning'] = t('<p><strong>Process interruption.</strong><br><br>All processes have been interrupted by some error caused in the import.<p>');//Todos los proccesos se ha interrumpido por algun error ocasionado en la importación.
            }

            $context['finished'] = 1;
            
        }
    }

    /**
     * @inheritDoc
     */
    public function postComplaints(int $nid): bool{

        //$this->login();

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
        $response = $this->httpClient($signature, 'POST', 'queja/', $data);

        if(isset($response['code'])){

            $this->logger->get('API SMFC')->warning("Code: %code Mensaje crear radicado: %message <br> Se ha producido un error al crear radicado No. %settled como cliente web services en el sistema <strong> API SMFC.", ['%code' => $response['code'], '%message' => $response['message'], '%settled' => $codComplaints]);

            return FALSE;

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
                        $response = $this->httpClient($signature, 'POST', 'storage/', $data);

                        if(isset($response['code'])){

                            $this->logger->get('API SMFC')->warning("Code: %code Mensaje anexo radicado: %message <br> Se ha producido un error al crear el anexo del radicado No. %settled como cliente web services en el sistema <strong> API SMFC.", ['%code' => $response['code'], '%message' => $response['message'], '%settled' => $codComplaints]);

                        }

                    }

                }

            }

            //Guardamos el valor API SMFC para el radicado e informamos su estado.
            $nodeStorage->field_pqrsd_api_smfc->value = 'enviado';
            $nodeStorage->save();
         
            return TRUE;
        }

    }

    /**
     * @inheritDoc
     */
    public function putComplaints(int $nid): bool{

        //$this->login();

        //Carga de informacion del nodo por medio de su $nid
        $nodeStorage = $this->entityTypeManager->getStorage('node')->load($nid);

        //Se tiene que validar que la fecha de cierre no sea igual a la fecha actual
        $now = $this->dateFormatter->format(strtotime(new DrupalDateTime()), 'custom', 'Y-m-d');
        
        $dateResponseAft = isset($nodeStorage->get("field_pqrsd_fecha_respuesta")->getValue()[0]['value']) ?$this->dateFormatter->format(strtotime(new DrupalDateTime($nodeStorage->get("field_pqrsd_fecha_respuesta")->getValue()[0]['value'])),'custom', 'Y-m-d') : $now;       

        if(isset($nodeStorage->get('field_pqrsd_respuesta')->getValue()[0]['value']) && $now !== $dateResponseAft){

            //Codigo de Radicado(Queja o Reclamos) ===== ===== 
            $codComplaints = $nodeStorage->get("field_pqrsd_numero_radicado")->getValue()[0]['value'];

            //Sexo
            $sexo = $nodeStorage->get("field_pqrsd_sexo")->getValue()[0]['value'];

            //lgbti
            $lgtbi = $nodeStorage->get("field_pqrsd_lgtbi")->getValue()[0]['value'];

            //Producto ===== =====
            $codProduct = $nodeStorage->get("field_pqrsd_nombre_producto")->getValue()[0]['value'];

            //Motivo
            $codMotive = $nodeStorage->get("field_pqrsd_motivo")->getValue()[0]['value'];

            //Fecha de cierre y de actualizacion
            $dateResponse = $nodeStorage->get("field_pqrsd_fecha_respuesta")->getValue()[0]['value'];

            //A favor de
            $infavorof = $nodeStorage->get("field_pqrsd_respuesta_a_favor")->getValue()[0]['value'];

            $infavorof = $infavorof === "findeter" ? 1 : 3;

            //Anexo respuesta de la queja o reclamo
            $anexFileResponseFile = $nodeStorage->get("field_pqrsd_respuesta_archivos")->getValue();

            //Tutela
            $wardShip = $nodeStorage->get("field_pqrsd_tutela")->getValue()[0]['value'];

            //Ente de control
            $entityControl = $nodeStorage->get("field_pqrsd_entes_control")->getValue()[0]['value'];
            
            $dataSignature = '{"codigo_queja": "'.$codComplaints.'", '.
                '"sexo": "'.$sexo.'", '.
                '"lgbtiq": "'.$lgtbi.'", '.
                '"condicion_especial": "98", '. //
                '"canal_cod": null, '. //
                '"producto_cod": "'.$codProduct.'", '.
                '"macro_motivo_cod": "'.$codMotive.'", '.
                '"estado_cod": "4", '.
                '"fecha_actualizacion": "'.$dateResponse.'", '.
                '"producto_digital": "2", '.
                '"a_favor_de": "'.$infavorof.'", '.
                '"aceptacion_queja": null, '.
                '"rectificacion_queja": null, '.
                '"desistimiento_queja": null, '. //
                '"prorroga_queja": null, '.
                '"admision": "9", '.
                '"documentacion_rta_final": true, '.
                '"anexo_queja": true, '.
                '"fecha_cierre": "'.$dateResponse.'", '.
                '"tutela": "'.$wardShip.'", '.
                '"ente_control": "'.$entityControl.'", '.
                '"marcacion": null, '.
                '"queja_expres": "2"}';//
            
            //Firma encrypt sha256 en funcion de hmac.===== ===== 
            $signature = strtoupper(hash_hmac('sha256',$dataSignature,$this->secretKey,FALSE));

            //Se envia la data para poder validar algunos valores
            $data = ['code' => $codComplaints, 'data' => $dataSignature];

            //Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== 
            $response = $this->httpClient($signature, 'PUT', 'queja/', $data);

            if(isset($response['code'])){

                $this->logger->get('API SMFC')->warning("Code: %code Mensaje actualizacion de radicado: %message <br> Se ha producido un error al actualizar radicado No. %settled como cliente web services en el sistema <strong> API SMFC.", ['%code' => $response['code'], '%message' => $response['message'], '%settled' => $codComplaints]);
    
                return FALSE;
    
            }else{

                $file_storage = $this->entityTypeManager->getStorage('file');

                foreach ($anexFileResponseFile as $file) {

                    if(sizeof($file)){
                        $anexFile = $file_storage->load($file['target_id']);

                        /* Se crea una variable de tipo string fomentado a uno de tipo objeto. ===== ===== */
                        $dataSignature = '{"type": "'.$anexFile->getMimeType().'", '.
                        '"codigo_queja": "'.$codComplaints.'"}';

                        //Firma encrypt sha256 en funcion de hmac.===== ===== 
                        $signature = strtoupper(hash_hmac('sha256',$dataSignature,$this->secretKey,FALSE));

                        $data = ['file' => $anexFile->getFileUri(), 'type' => $anexFile->getMimeType(), 'code' => $codComplaints];

                        //Se obtiene la respuesta en peticion Http consumiendo o enviando la data a SMFC. Client Web Service===== ===== 
                        $response = $this->httpClient($signature, 'POST', 'storage/', $data);

                        if(isset($response['code'])){

                            $this->logger->get('API SMFC')->warning("Code: %code Mensaje anexo respuesta radicado: %message <br> Se ha producido un error al crear el anexo de respuesta radicado No. %settled como cliente web services en el sistema <strong> API SMFC.", ['%code' => $response['code'], '%message' => $response['message'], '%settled' => $codComplaints]);

                            return FALSE;

                        }

                    }

                }
             
                return TRUE;
            }
            
        }else{
            return FALSE;
        }
    }
}