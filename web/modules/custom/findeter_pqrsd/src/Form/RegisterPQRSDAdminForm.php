<?php

namespace Drupal\findeter_pqrsd\Form;

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

      $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'pqrsd');

      $arrayFields['info_radicado']['title_detail'] = 'Información del radicado';
      $arrayFields['info_radicado']['field_pqrsd_tipo_radicado']= 'open required';
      $arrayFields['info_radicado']['field_pqrsd_tipo_peticion'] = '';
      $arrayFields['info_radicado']['field_pqrsd_tipo_solicitante'] = 'required';
      $arrayFields['info_radicado']['field_pqrsd_rango_edad'] = 'close required';
      $arrayFields['info_radicado']['field_pqrsd_grupo_etnico'] = 'open';
      $arrayFields['info_radicado']['field_pqrsd_preferencial'] = '';
      $arrayFields['info_radicado']['field_pqrsd_tipo_discapacidad'] = 'close required';

      $arrayFields['info_person']['title_detail'] = 'Información de la persona';
      $arrayFields['info_person']['field_pqrsd_numero_id'] = 'open';
      $arrayFields['info_person']['field_pqrsd_tipo_documento'] = '';
      $arrayFields['info_person']['field_pqrsd_primer_nombre'] = '';
      $arrayFields['info_person']['field_pqrsd_segundo_nombre'] = '';
      $arrayFields['info_person']['field_pqrsd_primer_apellido'] = '';
      $arrayFields['info_person']['field_pqrsd_segundo_apellido'] = 'close';
      $arrayFields['info_person']['field_pqrsd_direccion'] = 'open';
      $arrayFields['info_person']['field_pqrsd_departamento'] = '';
      $arrayFields['info_person']['field_pqrsd_municipio'] = '';
      $arrayFields['info_person']['field_pqrsd_telefono'] = '';
      $arrayFields['info_person']['field_pqrsd_fax'] = '';
      $arrayFields['info_person']['field_pqrsd_email'] = '';
      $arrayFields['info_person']['field_pqrsd_nit'] = '';
      $arrayFields['info_person']['field_pqrsd_razon_social'] = '';
      $arrayFields['info_person']['field_pqrsd_tipo_empresa'] = 'close';

      $arrayFields['info_product']['title_detail'] = 'Información del producto';
      $arrayFields['info_product']['field_pqrsd_nombre_producto'] = 'open required';
      $arrayFields['info_product']['field_pqrsd_archivo'] = '';
      $arrayFields['info_product']['field_pqrsd_motivo'] = '';
      $arrayFields['info_product']['field_pqrsd_otros'] = 'close';
      $arrayFields['info_product']['field_pqrsd_descripcion'] = 'column required';

      $arrayFields['info_admin']['title_detail'] = 'Información administrativa';
      $arrayFields['info_admin']['field_pqrsd_canal_recepcion'] = 'open';
      $arrayFields['info_admin']['field_pqrsd_medio_respuesta'] = '';
      $arrayFields['info_admin']['field_pqrsd_forma_recepcion'] = 'close required';
      $arrayFields['info_admin']['field_asign'] = 'open';
      $arrayFields['info_admin']['field_pqrsd_palabras_clave'] = 'close';
      $arrayFields['info_admin']['field_pqrsd_autorizacion'] = 'required';
      $arrayFields['info_admin']['field_pqrsd_marketing'] = '';

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
                  $form[$idDetail][$idField]['#attributes'] = [
                    'placeholder'=>'Diligencie su '.strtolower($definitions[$idField]->getLabel())
                  ];
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
                  $form[$idDetail][$idField]['#attributes'] = [
                    'placeholder'=>'Diligencie su '.strtolower($definitions[$idField]->getLabel())
                  ];
                break;

                case 'file':
                  $fileSettings = $definitions[$idField]->getSettings();

                  $form[$idDetail][$idField]['#type'] = 'managed_file';
                  $form[$idDetail][$idField]['#cardinality'] = 3;
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
      
      $form['info_person']['field_pqrsd_departamento']['#type'] = 'select';
      $form['info_person']['field_pqrsd_departamento']['#options'] = $deparmentOptions;
      $form['info_person']['field_pqrsd_departamento']['#empty_option'] = '-Seleccione una opción-';
      $form['info_person']['field_pqrsd_departamento']['#default_value'] = NULL;
      $form['info_person']['field_pqrsd_departamento']['#source'] = 'admin';
      $form['info_person']['field_pqrsd_departamento']['#ajax'] = [
        'callback'  => 'callBackDeparment', 
        'event'     => 'change',
        'progress'  => [
          'message' => 'Recupersando municipios...',
        ],      
      ];  

      $departmentValue = false;
      if($form_state->getValue('field_pqrsd_departamento') != ''){
        $departmentValue = $form_state->getValue('field_pqrsd_departamento');
      }

      $form['info_person']['field_pqrsd_municipio']['#type'] = 'select';
      $form['info_person']['field_pqrsd_municipio']['#prefix'] = '<div id="output-municipalities">';
      $form['info_person']['field_pqrsd_municipio']['#suffix'] = '</div>';
      $form['info_person']['field_pqrsd_municipio']['#empty_option'] = '-Seleccione una opción-';
      $form['info_person']['field_pqrsd_municipio']['#default_value'] = NULL;

      if ($departmentValue) {
        $form['info_person']['field_pqrsd_municipio']['#options'] = getTaxonomyTermsForm($departmentValue);
      }else{
        $form['info_person']['field_pqrsd_municipio']['#options'] = [];
      }

      // change type of field for "Medio de respuesta" field
      $form['info_admin']['field_pqrsd_medio_respuesta']['#type'] = 'radios';

      // define params for "Autorization" checkbox
      $form['info_admin']['field_pqrsd_autorizacion']['#type'] = 'checkbox';
      $form['info_admin']['field_pqrsd_autorizacion']['#title'] = $definitions['field_pqrsd_autorizacion']->getSetting('allowed_values')['autorizacion_findeter'];
      $form['info_admin']['field_pqrsd_autorizacion']['#value'] = 'autorizacion_findeter';
      unset($form['info_admin']['field_pqrsd_autorizacion']['#empty_option']);
      unset($form['info_admin']['field_pqrsd_autorizacion']['#options']);

      // define params for "Marketing" checkbox
      $form['info_admin']['field_pqrsd_marketing']['#type'] = 'checkbox';
      $form['info_admin']['field_pqrsd_marketing']['#title'] = $definitions['field_pqrsd_marketing']->getSetting('allowed_values')['autorizacion_marketing'];
      unset($form['info_admin']['field_pqrsd_marketing']['#empty_option']);
      unset($form['info_admin']['field_pqrsd_marketing']['#options']);


      // define custom placeholders
      $form['info_product']['field_pqrsd_otros']['#attributes'] = ['placeholder'=>'Especifique cuál'];
      $form['info_product']['field_pqrsd_descripcion']['#attributes'] = [
        'placeholder'=>'Escriba el detalle de su Petición, Queja, Reclamo, Sugerencia o Denuncia.','id'=>'edit-field-pqrsd-descripcion'
      ];
      $form['info_admin']['field_pqrsd_palabras_clave']['#attributes'] = ['placeholder'=>'Diligencie las palabras clave'];
      $form['info_product']['field_pqrsd_archivo']['#description'] = 'Puede registrar hasta 4 archivos.';


      $url = Url::fromRoute('findeter_pqrsd.userslist');
      $modalLink = Link::fromTextAndUrl('Lista de usuarios para asignar', $url);
      $modalLink = $modalLink->toRenderable();
      $modalLink['#attributes'] = [
        'class' => ['use-ajax', 'btn', 'btn-outline-primary'],
        'data-dialog-type' => ['modal'],
        'data-dialog' => ['modal'],
        'data-dialog-options' => ['{"width":800}'],
      ];

      //create new field for asign user
      $form['info_admin']['field_asign']['#type'] = 'textfield';
      $form['info_admin']['field_asign']['#title'] = 'Asignar a';
      $form['info_admin']['field_asign']['#autocomplete_route_name'] = 'findeter_pqrsd.autocomplete.users';
      $form['info_admin']['field_asign']['#attributes'] = [
        'placeholder'=>'Diligencie el nombre de usuario'
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

      $newPQRSD = Url::fromRoute('findeter_pqrsd.register_pqrsd_admin');
      $newPQRSDLink = Link::fromTextAndUrl('Crear una nueva radicatoria', $newPQRSD);
      $newPQRSDLink = $newPQRSDLink->toRenderable();

      $adminPage = Url::fromRoute('view.pqrsd.page_1');
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

    $form['#attached']['library'][] = 'findeter_pqrsd/admin_pages';

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
    $newRequest->set('title', 'User request - '.date('U'));

    $values = $form_state->getValues();
    foreach($values as $key=>$value){

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
    $form_state->setRebuild();
    $form['new_nid'] = $newRequest->id();
    $form_state->setValue('new_nid',$newRequest->id());

  } 

}