<?php

namespace Drupal\findeter_user_form_request\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Link;
use Drupal\Core\Url;

use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;


class RegisterPQRSDAdminForm extends FormBase {

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

    $new_nid = $form_state->getValue('new_nid');

    if($new_nid == NULL){

      $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'user_request');

      $arrayFields['info_radicado']['title_detail'] = 'Información del radicado';
      $arrayFields['info_radicado']['field_type_form']= 'open required';
      $arrayFields['info_radicado']['field_type_request'] = '';
      $arrayFields['info_radicado']['field_type_requester'] = 'required';
      $arrayFields['info_radicado']['field_age_range'] = 'close required';
      $arrayFields['info_radicado']['field_ethnic_group'] = 'open required';
      $arrayFields['info_radicado']['field_preferential_attention'] = 'required';
      $arrayFields['info_radicado']['field_type_handicap'] = 'close required';

      $arrayFields['info_person']['title_detail'] = 'Información de la persona';
      $arrayFields['info_person']['field_person_number_id'] = 'open';
      $arrayFields['info_person']['field_person_type_id'] = '';
      $arrayFields['info_person']['field_person_first_name'] = '';
      $arrayFields['info_person']['field_person_second_name'] = '';
      $arrayFields['info_person']['field_person_first_lastname'] = '';
      $arrayFields['info_person']['field_person_second_lastname'] = 'close';
      $arrayFields['info_person']['field_person_address'] = 'open';
      $arrayFields['info_person']['field_person_deparment'] = '';
      $arrayFields['info_person']['field_person_municipality'] = '';
      $arrayFields['info_person']['field_person_phone_contact'] = '';
      $arrayFields['info_person']['field_person_fax_contact'] = '';
      $arrayFields['info_person']['field_person_email'] = '';
      $arrayFields['info_person']['field_legal_nit'] = '';
      $arrayFields['info_person']['field_legal_business_name'] = '';
      $arrayFields['info_person']['field_legal_type_business'] = 'close';

      $arrayFields['info_product']['title_detail'] = 'Información del producto';
      $arrayFields['info_product']['field_product_name'] = 'open required';
      $arrayFields['info_product']['field_request_files'] = '';
      $arrayFields['info_product']['field_request_reason'] = '';
      $arrayFields['info_product']['field_request_other'] = 'close';
      $arrayFields['info_product']['field_request_description'] = 'column required';

      $arrayFields['info_admin']['title_detail'] = 'Información administrativa';
      $arrayFields['info_admin']['field_request_receiving_channel'] = 'open';
      $arrayFields['info_admin']['field_contact_answer_channel'] = '';
      $arrayFields['info_admin']['field_request_reception_form'] = 'close required';
      $arrayFields['info_admin']['field_asign'] = 'open';
      $arrayFields['info_admin']['field_request_keywords'] = 'close';
      $arrayFields['info_admin']['field_authorization'] = 'required';
      $arrayFields['info_admin']['field_request_marketing'] = '';

      $counter = 1;
      foreach($arrayFields as $idDetail=>$field){
        
        // define detail container
        $form[$idDetail] = [
          '#type'  => 'details',
          '#open'  => TRUE
        ];

        foreach($field as $idField=>$operations){
          if($idField=='title_detail'){
            $form[$idDetail]['#title'] = $operations;
          }else{

            // asign the position in the form
            $form[$idDetail][$idField]['#weight'] = $counter;
            $counter++;

            if(isset($definitions[$idField])){
              switch($definitions[$idField]->getType()){
                case 'string':
                  $form[$idDetail][$idField]['#type'] = 'textfield';
                  $form[$idDetail][$idField]['#title'] = $definitions[$idField]->getLabel();
                  $form[$idDetail][$idField]['#description'] = $definitions[$idField]->getDescription();
                break;

                case 'list_string':
                  $form[$idDetail][$idField]['#type'] = 'select';
                  $form[$idDetail][$idField]['#title'] = $definitions[$idField]->getLabel();
                  $form[$idDetail][$idField]['#empty_option'] = '-Seleccione una opción-';
                  $form[$idDetail][$idField]['#options'] = $definitions[$idField]->getSetting('allowed_values');
                  $form[$idDetail][$idField]['#description'] = $definitions[$idField]->getDescription();
                break;

                case 'email':
                  $form[$idDetail][$idField]['#type'] = 'email';
                  $form[$idDetail][$idField]['#title'] = $definitions[$idField]->getLabel();
                  $form[$idDetail][$idField]['#description'] = $definitions[$idField]->getDescription();
                break;

                case 'file':
                  $fileSettings = $definitions[$idField]->getSettings();

                  $form[$idDetail][$idField]['#type'] = 'managed_file';
                  $form[$idDetail][$idField]['#cardinality'] = 3;
                  $form[$idDetail][$idField]['#upload_location'] = 'public://'.$fileSettings['file_directory'];
                  $form[$idDetail][$idField]['#multiple'] = true;
                  $form[$idDetail][$idField]['#title'] = $definitions[$idField]->getLabel();
                  $form[$idDetail][$idField]['#upload_validators'] = [
                    'file_validate_extensions' => [$fileSettings['file_extensions']],
                    'file_validate_size'       => 20971520,
                  ];
                  $form[$idDetail][$idField]['#description'] = $definitions[$idField]->getDescription();
                break;

                case 'string_long':
                  $form[$idDetail][$idField]['#type'] = 'textarea';
                  $form[$idDetail][$idField]['#maxlength'] = 3500;
                  $form[$idDetail][$idField]['#title'] = $definitions[$idField]->getLabel();
                  $form[$idDetail][$idField]['#empty_option'] = '-Seleccione una opción-';
                  $form[$idDetail][$idField]['#options'] = $definitions[$idField]->getSetting('allowed_values');
                  $form[$idDetail][$idField]['#description'] = '<div>Puede ingresar hasta un máximo de 3500 caracteres. <br>Caracteres ingresados: <span class="counter-char">-</span>, máximo 3500 caracteres.</div>';
                break;

                case 'entity_reference':
                  $entitySettings = $definitions[$idField]->getSettings();

                  $form[$idDetail][$idField]['#type'] = 'entity_autocomplete';
                  $form[$idDetail][$idField]['#target_type'] = $entitySettings['target_type'];
                  $form[$idDetail][$idField]['#title'] = $definitions[$idField]->getLabel();
                  $form[$idDetail][$idField]['#selection_settings'] = array(
                    'target_bundles' => $entitySettings['handler_settings']['target_bundles'],
                  );
                  $form[$idDetail][$idField]['#description'] = $definitions[$idField]->getDescription();
                break;
    
                default:
                break;
              }  
            }


            $listOp = explode(' ',$operations);
            foreach($listOp as $operation){

              switch($operation){
                case 'open':
                  $form[$idDetail][$idField]['#prefix'] = '<div class="col">';
                break;
    
                case 'close':
                  $form[$idDetail][$idField]['#suffix'] = '</div>';
                break;
    
                case 'column':
                  $form[$idDetail][$idField]['#prefix'] = '<div class="col">';
                  $form[$idDetail][$idField]['#suffix'] = '</div>';
                break;

                case 'required':
                  $form[$idDetail][$idField]['#required'] = true;
                break;

                default:
                break;

              }
            }
            
          }

        }

      }

      // define values and parameters for Department/municipalities
      $deparmentOptions = getTaxonomyTermsForm(0);
      
      $form['info_person']['field_person_deparment']['#type'] = 'select';
      $form['info_person']['field_person_deparment']['#options'] = $deparmentOptions;
      $form['info_person']['field_person_deparment']['#empty_option'] = '-Seleccione una opción-';
      $form['info_person']['field_person_deparment']['#default_value'] = NULL;
      $form['info_person']['field_person_deparment']['#source'] = 'admin';
      $form['info_person']['field_person_deparment']['#ajax'] = [
        'callback'  => 'callBackDeparment', 
        'event'     => 'change',
        'progress'  => [
          'message' => 'Recupersando municipios...',
        ],      
      ];  

      $departmentValue = false;
      if($form_state->getValue('field_person_deparment') != ''){
        $departmentValue = $form_state->getValue('field_person_deparment');
      }

      $form['info_person']['field_person_municipality']['#type'] = 'select';
      $form['info_person']['field_person_municipality']['#prefix'] = '<div id="output-municipalities">';
      $form['info_person']['field_person_municipality']['#suffix'] = '</div>';
      $form['info_person']['field_person_municipality']['#empty_option'] = '-Seleccione una opción-';
      $form['info_person']['field_person_municipality']['#default_value'] = NULL;

      if ($departmentValue) {
        $form['info_person']['field_person_municipality']['#options'] = getTaxonomyTermsForm($departmentValue);
      }else{
        $form['info_person']['field_person_municipality']['#options'] = [];
      }

      // change type of field for "Medio de respuesta" field
      $form['info_admin']['field_contact_answer_channel']['#type'] = 'radios';

      // define params for "Autorization" checkbox
      $form['info_admin']['field_authorization']['#type'] = 'checkbox';
      $form['info_admin']['field_authorization']['#title'] = $definitions['field_authorization']->getSetting('allowed_values')['autorizacion_findeter'];
      $form['info_admin']['field_authorization']['#value'] = 'autorizacion_findeter';
      unset($form['info_admin']['field_authorization']['#empty_option']);
      unset($form['info_admin']['field_authorization']['#options']);

      // define params for "Marketing" checkbox
      $form['info_admin']['field_request_marketing']['#type'] = 'checkbox';
      $form['info_admin']['field_request_marketing']['#title'] = $definitions['field_request_marketing']->getSetting('allowed_values')['autorizacion_marketing'];
      unset($form['info_admin']['field_request_marketing']['#empty_option']);
      unset($form['info_admin']['field_request_marketing']['#options']);

      //create new field for asign user
      $form['info_admin']['field_asign']['#type'] = 'textfield';
      $form['info_admin']['field_asign']['#title'] = 'Asignar a';
      $form['info_admin']['field_asign']['#autocomplete_route_name'] = 'findeter_user_form_request.autocomplete.users';


      $url = Url::fromRoute('findeter_user_form_request.userslist');
      $modalLink = Link::fromTextAndUrl('Lista de usuarios para asignar', $url);
      $modalLink = $modalLink->toRenderable();
      $modalLink['#attributes'] = [
        'class' => ['use-ajax', 'btn', 'btn-outline-primary'],
        'data-dialog-type' => ['modal'],
        'data-dialog' => ['modal'],
        'data-dialog-options' => ['{"width":800}'],
      ];

      $form['info_admin']['field_asign']['#description'] = "Puede consultar también la ".render($modalLink);

      $form['actions'] = [
        '#type' => 'actions',
      ];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => 'Guardar',
      ];

      

    }else{

      $newPQRSD = Url::fromRoute('findeter_user_form_request.register_pqrsd_admin');
      $newPQRSDLink = Link::fromTextAndUrl('Crear una nueva radicatoria', $newPQRSD);
      $newPQRSDLink = $newPQRSDLink->toRenderable();

      $adminPage = Url::fromRoute('view.user_request_views.page_1');
      $adminPageLink = Link::fromTextAndUrl('Ir al panel de administración', $adminPage);
      $adminPageLink = $adminPageLink->toRenderable();


      $form['#markup'] = '
        <div class="success">Se registró la PQRSD satisfactoriamente<br>
            El número de radicado es:
              <span>'.$new_nid.'</span>
        </div>
        <div class="actions-success">Puede:
          <div class="link">'.render($newPQRSDLink).'</div>
          <div class="link">'.render($adminPageLink).'</div>
        </div>';
    }

    $form['#attached']['library'][] = 'findeter_user_form_request/admin_pages';

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

    $typeForm = $form_state->getValue('field_type_form');
    if($typeForm=='Peticiones'){
      $typeRequest = $form_state->getValue('field_type_request');
      if($typeRequest ==''){
        $form_state->setErrorByName('field_type_request', 'Debe seleccionar el tipo de petición');
      }
    }

    $typeRequester = $form_state->getValue('field_type_requester');
    if($typeRequester != 'anonimo'){
      if($form_state->getValue('field_person_number_id') == ''){
        $form_state->setErrorByName('field_person_number_id', 'Debe ingresar el Número identificación');
      }

      if($form_state->getValue('field_person_type_id') == ''){
        $form_state->setErrorByName('field_person_type_id', 'Debe seleccionar el Tipo documento ID');
      }

      if($form_state->getValue('field_person_first_name') == ''){
        $form_state->setErrorByName('field_person_first_name', 'Debe ingresar el Primer nombre');
      }

      if($form_state->getValue('field_person_first_lastname') == ''){
        $form_state->setErrorByName('field_person_first_lastname', 'Debe ingresar el Primer apellido');
      }

      if($form_state->getValue('field_person_address') == ''){
        $form_state->setErrorByName('field_person_address', 'Debe ingresar la Dirección correspondencia');
      }
      
      if($form_state->getValue('field_person_deparment') == ''){
        $form_state->setErrorByName('field_person_deparment', 'Debe seleccionar el Departamento');
      }

      if($form_state->getValue('field_person_municipality') == ''){
        $form_state->setErrorByName('field_person_municipality', 'Debe seleccionar el Municipio');
      }

      if($form_state->getValue('field_person_phone_contact') == ''){
        $form_state->setErrorByName('field_person_phone_contact', 'Debe ingresar el Teléfono de contacto');
      }

      if($form_state->getValue('field_person_email') == ''){
        $form_state->setErrorByName('field_person_email', 'Debe ingresar el Correo electrónico');
      }

      if($typeRequester != 'juridica'){

        if($form_state->getValue('field_legal_nit') == ''){
          $form_state->setErrorByName('field_legal_nit', 'Debe ingresar el NIT');
        }

        if($form_state->getValue('field_legal_business_name') == ''){
          $form_state->setErrorByName('field_legal_business_name', 'Debe ingresar la Razón social');
        }

        if($form_state->getValue('field_legal_type_business') == ''){
          $form_state->setErrorByName('field_legal_type_business', 'Debe ingresar el Tipo de empresa');
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
    $newRequest = Node::create(['type' => 'user_request']);
    $newRequest->set('title', 'User request - '.date('U'));

    $values = $form_state->getValues();
    foreach($values as $key=>$value){

      if(strpos($key,"field_")!== false){
        if($value!='' && $key!='field_asign'){
          $newRequest->set($key, $value);
        }
      }

      // store all files
      if($key == 'field_request_files' && !empty($value)){
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

    $newRequest->field_request_designatations[] = $user->getUsername().' | '.$user->id().' | '.date('j/m/Y H:i:s');

    // retrive user id from text form field
    if($usrAsignField != ''){
      $pattern = "/\((.*)\)/";
      preg_match( $pattern, $usrAsignField, $userArray );
      if($userAsign = \Drupal\user\Entity\User::load($userArray[1])){
        if($userAsign->id() != $user->id()){
          $user = $userAsign;
          $newRequest->field_request_designatations[] = $user->getUsername().' | '.$user->id().' | '.date('j/m/Y H:i:s');
        }
      }
    }
    // asign the last user retrived lines up
    $newRequest->uid = $user->id(); 

    // define date of answer
    switch ($form_state->getValue('field_type_form')) {
      case 'Quejas':
        $newDate = date('Y-m-d\TH:i:s',strtotime('+15 days'));
        $newRequest->set('field_request_answer_date', $newDate);
      break;

      case 'Reclamos':
        $newDate = date('Y-m-d\TH:i:s',strtotime('+15 days'));
        $newRequest->set('field_request_answer_date', $newDate);
      break;

      case 'Sugerencias':
        $newDate = date('Y-m-d\TH:i:s',strtotime('+15 days'));
        $newRequest->set('field_request_answer_date', $newDate);
      break;

      case 'Denuncias':
        $newDate = date('Y-m-d\TH:i:s',strtotime('+15 days'));
        $newRequest->set('field_request_answer_date', $newDate);
      break;

      case 'Peticiones':
        
        switch($form_state->getValue('field_type_request')){
          case 'general':
            $newDate = date('Y-m-d\TH:i:s',strtotime('+30 days'));
          break;
          case 'particular':
            $newDate = date('Y-m-d\TH:i:s',strtotime('+20 days'));
          break;
          case 'informacion':
            $newDate = date('Y-m-d\TH:i:s',strtotime('+35 days'));
          break;
          case 'publica':
            $newDate = date('Y-m-d\TH:i:s',strtotime('+10 days'));
          break;
        }
        
        $newRequest->set('field_request_answer_date', $newDate);
      break;

      default:
        break;
    }

    $newRequest->enforceIsNew();
    $newRequest->save();
    $form_state->setRebuild();
    $form['new_nid'] = $newRequest->id();
    $form_state->setValue('new_nid',$newRequest->id());

  } 

}