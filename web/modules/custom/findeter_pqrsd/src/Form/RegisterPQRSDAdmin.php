<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Link;
use Drupal\Core\Cache\Cache;

class RegisterPQRSDAdmin extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'register_pqrsd_admin';
  }

   /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    // see findeter_pqrsd.module
    $form = buildPQRSDform();

    /*$valueUser = $form_state->getUserInput();

    if(!empty($form_state->getValue('field_pqrsd_archivo'))){

      $nowTimeStamp = \Drupal::time()->getCurrentTime();
      $date = date('Y-m-d', $nowTimeStamp);
      $nidHours = 'T'.date('H', $nowTimeStamp).'---'.$valueUser['field_pqrsd_numero_id'];
      // Get the definitions
      $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'pqrsd');
      $fileSettings = $definitions['field_pqrsd_archivo']->getSettings();

      $form['info_product']['field_pqrsd_archivo']['#upload_location'] = 'private://pqrsd/'.$valueUser['field_pqrsd_tipo_radicado'].'/'.$date.'/'.$nidHours.'/';

      $form['info_product']['field_pqrsd_archivo']['#upload_validators'] = [
        'file_validate_extensions' => [($valueUser['field_pqrsd_tipo_radicado'] == 'Quejas' ||
        $valueUser['field_pqrsd_tipo_radicado'] == 'Reclamos') ? \Drupal::service('api.smfc')->getExtFile() : $fileSettings['file_extensions']],
        'file_validate_size'       => [20971520],
      ];

    }*/

    $departmentValue = false;

    $departmentValue = $form_state->getValue('field_pqrsd_departamento');
    if ($departmentValue) {
      $form['info_person']['field_pqrsd_municipio_container']['field_pqrsd_municipio_select']['#options'] = getTaxonomyTermsForm($departmentValue);
    }

    return $form;

  }

  /**
   * Validate the title and the checkbox of the form
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $typeForm = $form_state->getValue('field_pqrsd_tipo_radicado');
    if($typeForm=='Peticiones'){
      $typeRequest = $form_state->getValue('field_pqrsd_tipo_peticion');
      if($typeRequest ==''){
        $form_state->setErrorByName('field_pqrsd_tipo_peticion', 'Debe seleccionar el tipo de petición');
      }
    }

    $typeRequester = $form_state->getValue('field_pqrsd_tipo_solicitante');
    if($typeRequester != 'anonimo'){
      if($form_state->getValue('field_pqrsd_numero_id') == ''){
        $form_state->setErrorByName('field_pqrsd_numero_id', 'Debe ingresar el Número identificación');
      }else{
        if(!filter_var($form_state->getValue('field_pqrsd_numero_id'), FILTER_VALIDATE_INT)){
          $form_state->setErrorByName('field_pqrsd_numero_id', 'Debe ingresar un número válido');
        }
      }

      if($form_state->getValue('field_pqrsd_tipo_documento') == ''){
        $form_state->setErrorByName('field_pqrsd_tipo_documento', 'Debe seleccionar el Tipo documento ID');
      }

      if($form_state->getValue('field_pqrsd_primer_nombre') == ''){
        $form_state->setErrorByName('field_pqrsd_primer_nombre', 'Debe ingresar el Primer nombre');
      }

      if($form_state->getValue('field_pqrsd_primer_apellido') == ''){
        $form_state->setErrorByName('field_pqrsd_primer_apellido', 'Debe ingresar el Primer apellido');
      }

      if($form_state->getValue('field_pqrsd_direccion') == ''){
        $form_state->setErrorByName('field_pqrsd_direccion', 'Debe ingresar la Dirección correspondencia');
      }

      if($form_state->getValue('field_pqrsd_departamento') == ''){
        $form_state->setErrorByName('field_pqrsd_departamento', 'Debe seleccionar el Departamento');
      }

      if($form_state->getValue('field_pqrsd_municipio') == ''){
        $form_state->setErrorByName('field_pqrsd_municipio', 'Debe seleccionar el Municipio');
      }

      if($form_state->getValue('field_pqrsd_telefono') == ''){
        $form_state->setErrorByName('field_pqrsd_telefono', 'Debe ingresar el Teléfono de contacto');
      }else{
        if(!filter_var($form_state->getValue('field_pqrsd_telefono'), FILTER_VALIDATE_INT)){
          $form_state->setErrorByName('field_pqrsd_telefono', 'Debe ingresar un número válido');
        }
      }

      if($form_state->getValue('field_pqrsd_email') == ''){
        $form_state->setErrorByName('field_pqrsd_email', 'Debe ingresar el Correo electrónico');
      }else{
        if(!filter_var($form_state->getValue('field_pqrsd_email'), FILTER_VALIDATE_EMAIL)){
          $form_state->setErrorByName('field_pqrsd_email', 'Debe ingresar una dirección de correo válido');
        }
      }

      if($typeRequester == 'juridica' || $typeRequester == '2'){

        if($form_state->getValue('field_pqrsd_nit') == ''){
          $form_state->setErrorByName('field_pqrsd_nit', 'Debe ingresar el NIT');
        }else{
          if(!filter_var($form_state->getValue('field_pqrsd_nit'), FILTER_VALIDATE_INT)){
            $form_state->setErrorByName('field_pqrsd_nit', 'Debe ingresar un número válido');
          }
        }

        if($form_state->getValue('field_pqrsd_razon_social') == ''){
          $form_state->setErrorByName('field_pqrsd_razon_social', 'Debe ingresar la Razón social');
        }

        if($form_state->getValue('field_pqrsd_tipo_empresa') == ''){
          $form_state->setErrorByName('field_pqrsd_tipo_empresa', 'Debe ingresar el Tipo de empresa');
        }

      }

    }

  }


   /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $mailManager = \Drupal::service('plugin.manager.mail');
    $apiSmfc = \Drupal::service('api.smfc');
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $fileUsage = \Drupal::service('file.usage');

    //define new node of content type
    $newRequest = Node::create(['type' => 'pqrsd']);

    $numeroRadicado = generarNumeroRadicado($form_state->getValue('field_pqrsd_tipo_radicado'));

    // define title of node
    $newRequest->set('title', 'Radicado: '.$numeroRadicado.'.'.date('U'));

    // set "# radicado"
    /*// Si es un radicado de tipo Queja, se antepone el codigo de entidad de findeter para SMFC ===== ===== */
    if($form_state->getValue('field_pqrsd_tipo_radicado') == 'Quejas' ||
    $form_state->getValue('field_pqrsd_tipo_radicado') == 'Reclamos'){

      $numeroRadicado = $apiSmfc->getTipCodeEntity($numeroRadicado);
      $newRequest->set('field_pqrsd_numero_radicado', $numeroRadicado);

    }else{
      $newRequest->set('field_pqrsd_numero_radicado',$numeroRadicado);
    }

    // set "# radicado"
    //$newRequest->set('field_pqrsd_numero_radicado',$numeroRadicado);


    $values = $form_state->getValues();
    //FileSotorage que se carga a la entidad de tipo file
    $fileStorageArray = [];
    foreach($values as $key=>$value){

      if($key == 'field_pqrsd_palabras_clave'){
        $tag = $form_state->getValue('field_pqrsd_palabras_clave');
        if (empty($tag)) {
          //drupal_set_message("Tag is empty, nothing to do");
          $this->messenger()->addMessage($this->t("Tag is empty, nothing to do"), 'warning');
        }
        elseif (is_string($tag)) {
          //drupal_set_message("A term selected, tid = $tag");
          $this->messenger()->addMessage($this->t("A term selected, tid = %tag", ['%tag' => $tag]), 'warning');
        }
        elseif (isset($tag['entity']) && ($tag['entity'] instanceof Term)) {
          $entity = $tag['entity'];
          $entity->save();
          //drupal_set_message("A new term : " . $entity->id() . " : " . $entity->label());
          $this->messenger()->addMessage($this->t("A new term : %term_id : %term_label",['%term_id' => $entity->id(), '%term_label' => $entity->label()]));
        }
      }

      if(strpos($key,"field_")!== false){
        if($value!='' && $key!='field_asign'){
          $newRequest->set($key, $value);
        }
      }

      // store all files
      if($key == 'field_pqrsd_archivo' && !empty($value)){

        $fileStorage = $entityTypeManager->getStorage('file');

        foreach($value as $fid){

          if (!empty($fid)) {
            /*$file = File::load($fid);
            $file->setPermanent();
            $file->save();*/
            $file = $fileStorage->load($fid);

            //Se agegra el file entidad al arreglo filestorage declarado antes del ciclo for
            $fileStorageArray[$key] = $file;
          }

        }
      }

    }

    $usrAsignField = $form_state->getValue('field_asign');
    $user = \Drupal::currentUser();

    $newRequest->field_pqrsd_asignaciones[] = $user->getAccountName().' | '.$user->id().' | '.date('j/m/Y H:i:s');

    // define date of answer
    $datesConfigure = defineDatesSemaphore($values);
    $newRequest->set('field_pqrsd_fecha_roja', $datesConfigure['red']);
    $newRequest->set('field_pqrsd_fecha_naranja', $datesConfigure['orange']);

    //Instancia de recepcion @author 3desarrollo
    //Por defecto 2. Entidad
    $newRequest->set('field_pqrsd_instance_reception', 2);

    // retrive user id from text form field
    if($usrAsignField != ''){
      $pattern = "/\((.*)\)/";
      preg_match( $pattern, $usrAsignField, $userArray );
      if($userAsign = \Drupal\user\Entity\User::load($userArray[1])){
        if($userAsign->id() != $user->id()){
          $user = $userAsign;
          $newRequest->field_pqrsd_asignaciones[] = $user->getAccountName().' | '.$user->id().' | '.date('j/m/Y H:i:s');
        }

        $module = 'findeter_pqrsd';
        $key = 'asigned_pqrsd';
        $to = $user->getEmail();

        $userName = '';
        $form_state->getValue('field_pqrsd_primer_nombre');
        if($form_state->getValue('field_pqrsd_primer_nombre')){
          $userName .= $form_state->getValue('field_pqrsd_primer_nombre').' ';
        }

        if($form_state->getValue('field_pqrsd_primer_apellido')){
          $userName .= $form_state->getValue('field_pqrsd_primer_apellido');
        }

        $mailBody[] = '<p>Hola '.$user->getAccountName().'</p><br>';
        $mailBody[] = '<p>Le informamos que se le asignó una PQRSD para que le dé respuesta:</p>';
        $mailBody[] = '<div class="numero-radicado">
                        <b>No. Radicatoria: </b>'.$numeroRadicado.'<br>
                        <b>Cliente: </b>'.$userName.'<br>
                        <b>Fecha registro: </b>'.date('d/m/Y H:m:i').'<br>
                        <b>Fecha vencimiento respuesta: </b>'.$datesConfigure['red'].'
                      </div>';
        $mailBody[] = '<p>Le invitamos a gestionar esta PQRSD, iniciando sesión en la página administrativa del sistema</p><br>';
        $mailBody[] = '<hr>';
        $mailBody[] = 'Cordialmente,';
        $mailBody[] = 'Vicepresidencia comercial - Servicio al cliente';
        $mailBody[] = 'Findeter';
        $mailBody[] = '<a href="https://www.findeter.gov.co" target="_blank"><img onerror="this.remove();" alt="Findeter" src="https://www.findeter.gov.co/sites/default/files/webfinde/images/encabezado/logo.png" width="300" height="150"></a>';

        $params['message'] = implode('<br>',$mailBody);

        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $send = true;

        $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

      }
    }

    // asign the last user retrived lines up
    $newRequest->uid = $user->id();

    Cache::invalidateTags(['node_list:pqrsd']);//Invalidamos las tags creadas en la cache con cid findeter_pqrsd_statistics y que tengas este tag asociado
    $newRequest->enforceIsNew();
    $newRequest->save();

    /* Se agrega como archivos gestionados a file.usage  ===== ===== */
    foreach($fileStorageArray as $file){

      $fileUsage->add($file, 'findeter_pqrsd', 'node', $newRequest->id());

    }

    $url = Url::fromRoute('findeter_pqrsd.confirm_register_pqrsd_admin',['operation'=>'create','nid'=>$newRequest->id()]);
    $form_state->setRedirectUrl($url);

    if($form_state->getValue('field_pqrsd_email') != ''){

      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'findeter_pqrsd';
      $key = 'registered_pqrsd';
      $to = $form_state->getValue('field_pqrsd_email');

      $url = Url::fromRoute('findeter_pqrsd.status_pqrsd');
      $statusLink = Link::fromTextAndUrl('ingresar', $url);
      $statusLink = $statusLink->toRenderable();

      $mailBody[] = '<p>Reciba un cordial saludo de parte de Findeter.</p>';
      $mailBody[] = '<p>De antemano queremos agradecerle por haberse puesto en contacto con nosotros a traves del sistema de atención al usuario. Su opinión es muy importante para nosotros.</p>';
      $mailBody[] = '<p>Le informamos que su solicitud ha sido registrada satisfactoriamente con el código de radicado:<br><strong>'.$numeroRadicado.'</strong></p>';
      $mailBody[] = '<p>Con este código podrá <a href="https://www.findeter.gov.co/estado-pqrsd">ingresar</a> para consultar el estado de la misma y si es el caso ampliar o enviar información adicional</p>';
      $mailBody[] = '<hr>';
      $mailBody[] = 'Cordialmente,';
      $mailBody[] = 'Vicepresidencia comercial - Servicio al cliente';
      $mailBody[] = 'Findeter';
      $mailBody[] = '<a href="https://www.findeter.gov.co" target="_blank"><img onerror="this.remove();" alt="Findeter" src="https://www.findeter.gov.co/sites/default/files/webfinde/images/encabezado/logo.png" width="300" height="150"></a>';

      $params['message'] = implode('<br>',$mailBody);

      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = true;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

      if($result['result'] !== true){
        \Drupal::messenger()->addError('Ocurrió un problema al enviar el correo.');
      }
    }

    /* ============================================
    Se envia la peticion de PQRSD al sistema SMFC
    siempre y cuando el tipo de radicado sea una
    Queja o Reclamo.
    @author 3ddesarrollo
    =============================================== */
    if($form_state->getValue('field_pqrsd_tipo_radicado') == 'Quejas' ||
    $form_state->getValue('field_pqrsd_tipo_radicado') == 'Reclamos'){

      /* Se guarda los nid como variables de estado para que despues
      sea registrado en la API SMFC. ==== ====== */
      $state = \Drupal::service('state');
      $nid = $state->get('findeter_pqrsd.api_smfc_nid');

      if(is_null($nid)){

        $state->set('findeter_pqrsd.api_smfc_nid', [
          [
          "nid" => $newRequest->id(),
          "title" => $newRequest->getTitle(),
          "created" => $newRequest->getCreatedTime(),
          "smfc" => FALSE,
          ]
        ]);

      }else{
        $nid[] = [
        "nid" => $newRequest->id(),
        "title" => $newRequest->getTitle(),
        "created" => $newRequest->getCreatedTime(),
        "smfc" => FALSE,
        ];

        $state->set('findeter_pqrsd.api_smfc_nid', $nid);

      }
    }

  }

}
