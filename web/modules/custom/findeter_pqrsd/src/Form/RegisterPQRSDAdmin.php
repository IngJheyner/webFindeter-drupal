<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;

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

    $departmentValue = false;
    if($form_state->getValue('field_pqrsd_departamento') != ''){
      $departmentValue = $form_state->getValue('field_pqrsd_departamento');
    }

    if ($departmentValue) {
      $form['info_person']['field_pqrsd_municipio']['#options'] = getTaxonomyTermsForm($departmentValue);
    }else{
      $form['info_person']['field_pqrsd_municipio']['#options'] = [];
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

      if($typeRequester == 'juridica'){

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


    //define new node of content type
    $newRequest = Node::create(['type' => 'pqrsd']);

    $numeroRadicado = generarNumeroRadicado();

    // define title of node
    $newRequest->set('title', 'Radicado: '.$numeroRadicado.'.'.date('U'));

    // set "# radicado"
    $newRequest->set('field_pqrsd_numero_radicado',$numeroRadicado);


    $values = $form_state->getValues();
    foreach($values as $key=>$value){

      if($key == 'field_pqrsd_palabras_clave'){
        $tag = $form_state->getValue('field_pqrsd_palabras_clave');
        if (empty($tag)) {
          drupal_set_message("Tag is empty, nothing to do");
        }
        elseif (is_string($tag)) {
          drupal_set_message("A term selected, tid = $tag");
        }
        elseif (isset($tag['entity']) && ($tag['entity'] instanceof Term)) {
          $entity = $tag['entity'];
          $entity->save();
          drupal_set_message("A new term : " . $entity->id() . " : " . $entity->label());
        }
      }

      if(strpos($key,"field_")!== false){
        if($value!='' && $key!='field_asign'){
          $newRequest->set($key, $value);
        }
      }

      // store all files
      if($key == 'field_pqrsd_archivo' && !empty($value)){
        foreach($value as $fid){
          if (!empty($fid)) {
            $file = File::load($fid);
            $file->setPermanent();
            $file->save();
          }
        }
      }

    }

    $usrAsignField = $form_state->getValue('field_asign');
    $user = \Drupal::currentUser();

    $newRequest->field_pqrsd_asignaciones[] = $user->getUsername().' | '.$user->id().' | '.date('j/m/Y H:i:s');

    // retrive user id from text form field
    if($usrAsignField != ''){
      $pattern = "/\((.*)\)/";
      preg_match( $pattern, $usrAsignField, $userArray );
      if($userAsign = \Drupal\user\Entity\User::load($userArray[1])){
        if($userAsign->id() != $user->id()){
          $user = $userAsign;
          $newRequest->field_pqrsd_asignaciones[] = $user->getUsername().' | '.$user->id().' | '.date('j/m/Y H:i:s');
        }
      }
    }

    // asign the last user retrived lines up
    $newRequest->uid = $user->id();

    // define date of answer
    $datesConfigure = defineDatesSemaphore($values);
    $newRequest->set('field_pqrsd_fecha_roja', $datesConfigure['red']);
    $newRequest->set('field_pqrsd_fecha_naranja', $datesConfigure['orange']);

    $newRequest->enforceIsNew();
    $newRequest->save();

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

      $mailBody[] = 'Reciba un cordial saludo de parte de Findeter';
      $mailBody[] = 'De antemano queremos agradecerle por haberse puesto en contacto con nosotros a traves del sistema de atención al usuario. Su opinión es muy importante para nosotros.';
      $mailBody[] = 'Le informamos que su solicitud ha sido registrada satisfactoriamente con el código de radicado:';
      $mailBody[] = '<div class="numero-radicado">'.$numeroRadicado.'</div>';
      $mailBody[] = 'Con este código podrá '.render($statusLink).' para consultar el estado de la misma y si es el caso ampliar o enviar información adicional';
      $mailBody[] = '';
      $mailBody[] = 'Cordialmente,';
      $mailBody[] = 'Vicepresidencia comercial - Servicio al cliente';
      $mailBody[] = 'Findeter';

      $params['message'] = implode('<br>',$mailBody);

      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = true;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

      if($result['result'] !== true){
        \Drupal::messenger()->addError('Ocurrió un problema al enviar el correo.');
      }
    }

  }

}
