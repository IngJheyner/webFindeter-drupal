<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

class UpdatePQRSDAdmin extends FormBase {

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
    return 'update_pqrsd_admin';
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
  public function buildForm(array $form, FormStateInterface $form_state, $nid=NULL) {

    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $node = $storage->load($nid);

    // see findeter_pqrsd.module
    $form = buildPQRSDform();

    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid
    ];

    $departmentValue = false;
    if($form_state->getValue('field_pqrsd_departamento') != ''){
      $departmentValue = $form_state->getValue('field_pqrsd_departamento');
    }else{
      if(isset($node->get('field_pqrsd_departamento')->getValue()[0]['target_id'])){
        $departmentValue = $node->get('field_pqrsd_departamento')->getValue()[0]['target_id'];
      }
    }

    if ($departmentValue) {
      $form['info_person']['field_pqrsd_municipio']['#options'] = getTaxonomyTermsForm($departmentValue);
    }else{
      $form['info_person']['field_pqrsd_municipio']['#options'] = [];
    }

    $fieldSets[] = 'info_radicado';
    $fieldSets[] = 'info_person';
    $fieldSets[] = 'info_product';
    $fieldSets[] = 'info_admin';
    
    foreach($fieldSets as $fieldSet){
      $formFieldSet = $form[$fieldSet];

      foreach($formFieldSet as $id=>$fieldForm){

        if(strpos($id,'#') === false && $id != 'field_asign'){
          if(!empty($node->get($id)->getValue())){
            if(isset($node->get($id)->getValue()[0]['value'])){
              $form[$fieldSet ][$id]['#default_value'] = $node->get($id)->getValue()[0]['value'];
            }elseif(isset($node->get($id)->getValue()[0]['target_id'])){
              if($id == 'field_pqrsd_palabras_clave'){
                $keyWords = [];
                foreach($node->get($id)->getValue() as $term){
                  $keyWords[] = \Drupal\taxonomy\Entity\Term::load($term['target_id']);
                }
                $form[$fieldSet ][$id]['#default_value'] = $keyWords;
              }else{
                $form[$fieldSet ][$id]['#default_value'] = $node->get($id)->getValue()[0]['target_id'];
              }
              
            }

          }
        }
    
      }
    }

    // checkbor to expand date answer to "Incompleta" request type
    if(isset($node->get('field_pqrsd_tipo_peticion')->getValue()[0]['value']) &&
      $node->get('field_pqrsd_tipo_peticion')->getValue()[0]['value'] == 'incompleta'){
        $form['info_admin']['extend_date_asnwer'] = [
          '#type' => 'checkbox',
          '#title' => 'Se actualizó los datos de usuario (Extensión de fecha de respuesta)',
          '#value' => 'date_extended',
          '#weight' => 100
        ];
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

    //retrive node
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $node = $storage->load($form_state->getValue('nid'));

    $values = $form_state->getValues();
    foreach($values as $key=>$value){

      // set all field values
      if(strpos($key,"field_")!== false){
        if($value!='' && $key!='field_asign'){
          $node->set($key, $value);
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

    if($form_state->getValue('extend_date_asnwer') != ''){
      $values['field_pqrsd_tipo_peticion'] = 'incompleta|2';
      // define new extended date to answer
      $datesConfigure = defineDatesSemaphore($values);
      $node->set('field_pqrsd_fecha_roja', $datesConfigure['red']);
      $node->set('field_pqrsd_fecha_naranja', $datesConfigure['orange']);
    }

    $node->save();
    
    $url = Url::fromRoute('findeter_pqrsd.confirm_register_pqrsd',['operation'=>'update','nid'=>$form_state->getValue('nid')]);
    $form_state->setRedirectUrl($url);

  } 

}