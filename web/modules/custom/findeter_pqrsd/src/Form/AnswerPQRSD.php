<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\block\Entity\Block;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Class AnswerPQRSD.
 */
class AnswerPQRSD extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'answer_pqrsd';
  }

  /**
   * Building all elements in the form
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {

    $form_state->setCached(FALSE);

    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'pqrsd');

    $fileStorage = \Drupal::entityTypeManager()->getStorage('file');

    //Carga de datos del node
    $node = Node::load($nid);

    //Se ajusta para que los archivos de respuesta queden en la misma ruta de anexo de radicado.
    //Si no tiene algun anexo, se guarda el anexo respuesta con la fecha actual y id del peticionario.
    $anexTargetNid = $node->get('field_pqrsd_archivo')->getValue()[0]['target_id'] ??= null;

    if(is_null($anexTargetNid)){

      $dateTimestamp= strtotime(new DrupalDateTime());
      $date =  \Drupal::service('date.formatter')->format($dateTimestamp, 'custom', 'Y-m-d');
      $time =  str_replace(" ", "", (\Drupal::service('date.formatter')->format($dateTimestamp, 'custom', '\T\ H')));
      $time .= '---'.$node->get('field_pqrsd_numero_id')->getValue()[0]['value'];

      $anexPathResponse = 'public://pqrsd/'.$node->get('field_pqrsd_tipo_radicado')->getValue()[0]['value'].'/'.$date.'/'.$time;

    }else{
      $anexFile = $fileStorage->load($anexTargetNid);
      $anexUri = $anexFile->getFileUri();
      $anexPathResponse = \Drupal::service('file_system')->dirname($anexUri);
    }

    // Nid param to store in the new references
    $form['node_id'] = array(
      '#type' => 'hidden',
      '#value' => $nid,
    );

    $form['field_pqrsd_respuesta'] = [
      '#type'  => 'textarea',
      '#title' => 'Escriba la respuesta al requerimiento',
      '#required' => TRUE,
    ];
 
    $fileSettings = $definitions['field_pqrsd_respuesta_archivos']->getSettings();
    $form['field_pqrsd_respuesta_archivos'] = [
      '#type'            => 'managed_file',
      '#cardinality'     => 3,
      '#description'     => 'Puede registrar hasta 4 archivos',
      '#upload_location' => $anexPathResponse.'/respuesta/',
      '#multiple'        => TRUE,
      '#title'           => $definitions['field_pqrsd_respuesta_archivos']->getLabel(),
      '#upload_validators' => [
        'file_validate_extensions' => [$fileSettings['file_extensions']],
      ],
      '#required' => ($node->get('field_pqrsd_tipo_radicado')->getValue()[0]['value'] == "Quejas" || $node->get('field_pqrsd_tipo_radicado')->getValue()[0]['value'] == "Reclamos") ? TRUE : FALSE,
    ];

    //Se agrega nuevos campos para enviar a la API SMFC(motivo tutela y ente de control)
    if($node->get('field_pqrsd_tipo_radicado')->getValue()[0]['value'] == "Quejas" || $node->get('field_pqrsd_tipo_radicado')->getValue()[0]['value'] == "Reclamos"){

      $itemReason = $definitions['field_pqrsd_motivo']->getSetting('allowed_values');

      $optionsReasonSmfc = [];

      foreach ($itemReason as $key => $value) {
        if(is_numeric($key))
          $optionsReasonSmfc[$key] = $value;
      }

      $form['field_pqrsd_motivo'] = [
        '#type'    => 'select2',
        '#title'   => $definitions['field_pqrsd_motivo']->getLabel(),
        '#options' => $optionsReasonSmfc,
        '#empty_option' => '-Seleccione una opción-',
        '#default_value' => $node->get('field_pqrsd_motivo')->getValue()[0]['value'] ??= '',
        '#select2' => [
          'allowClear' => FALSE,
        ],
        '#required' => TRUE,
      ];

      $form['field_pqrsd_tutela'] = [
        '#type'    => 'select',
        '#title'   => $definitions['field_pqrsd_tutela']->getLabel(),
        '#options' => $definitions['field_pqrsd_tutela']->getSetting('allowed_values'),
        '#empty_option' => '-Seleccione una opción-',
        '#default_value' => $node->get('field_pqrsd_tutela')->getValue()[0]['value'] ??= '',
        '#required' => TRUE,
      ];

      $form['field_pqrsd_entes_control'] = [
        '#type'    => 'select',
        '#title'   => $definitions['field_pqrsd_entes_control']->getLabel(),
        '#options' => $definitions['field_pqrsd_entes_control']->getSetting('allowed_values'),
        '#empty_option' => '-Seleccione una opción-',
        '#default_value' => $node->get('field_pqrsd_entes_control')->getValue()[0]['value'] ??= '',
        '#required' => TRUE,
      ];

    }

    $form['field_pqrsd_respuesta_a_favor'] = [
      '#type'    => 'radios',
      '#title'   => $definitions['field_pqrsd_respuesta_a_favor']->getLabel(),
      '#options' => $definitions['field_pqrsd_respuesta_a_favor']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#required' => TRUE,
    ];

    // Submit with ajax event that after the operation, close the modal
    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Guardar',
      '#ajax'  => array(
        'callback' => '::ajaxCloseModal',
        'event'    => 'click',
      ),
    ];

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'findeter_pqrsd/global-scripts';

    $form_state->setRebuild();

    return $form;
  }

  /**
   * To validate values of the form
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }


  /**
   * Submit funtion (also close the modal window)
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  public function ajaxCloseModal(array &$form, FormStateInterface $form_state) {

    global $base_url;

    $formValues = $form_state->getValues();

    $node = Node::load($formValues['node_id']);

    $node->field_pqrsd_respuesta[] = $formValues['field_pqrsd_respuesta'];
    $node->field_pqrsd_respuesta_a_favor[] = $formValues['field_pqrsd_respuesta_a_favor'];
    $node->field_pqrsd_fecha_respuesta[] = date('Y-m-d\TH:i:s',strtotime('now'));
    $node->field_pqrsd_tutela[] = $formValues['field_pqrsd_tutela'];
    $node->field_pqrsd_entes_control[] = $formValues['field_pqrsd_entes_control'];
    
    $fileStorage = \Drupal::entityTypeManager()->getStorage('file');

    foreach($formValues['field_pqrsd_respuesta_archivos'] as $fid){
      $node->field_pqrsd_respuesta_archivos[] = $fid;

      /*$file = File::load($fid);
      $file->setPermanent();
      $file->save();*/
      $file = $fileStorage->load($fid);   
      \Drupal::service('file.usage')->add($file, 'findeter_pqrsd', 'node', $formValues['node_id']);

    }

    $node->save();    

    if(isset($node->get('field_pqrsd_email')->getValue()[0]['value']) &&
      $node->get('field_pqrsd_medio_respuesta')->getValue()[0]['value'] == 'email'){

      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'findeter_pqrsd';
      $key = 'answer_pqrsd';
      //$to = $form_state->getValue('field_pqrsd_email');
      $to = $node->get('field_pqrsd_email')->getValue()[0]['value'];

      $mailBody[] = '<p>Reciba un cordial saludo de parte de Findeter.</p>';
      $mailBody[] = '<p>Le informamos que hemos dado respuesta la PQRSD con número: <b>'.$node->get('field_pqrsd_numero_radicado')->getValue()[0]['value'].'</b></p><br><br>
      
      ';
      $mailBody[] = '<div class="numero-radicado"><strong>Estimado usuario: </strong><br>
      <blockquote>'.$node->get('field_pqrsd_respuesta')->getValue()[0]['value'] .'</blockquote>';

      if(sizeof($formValues['field_pqrsd_respuesta_archivos'])){
        $mailBody[] = '<p><strong>Nota:</strong> La respuesta a este radicado tiene <u>archivos adjuntos</u>, consulta con el numero de radicado asignado en el siguiente <a href="https://www.findeter.gov.co/estado-pqrsd">enlace</a></p>';
      }    

      $mailBody[] = '</div><hr>';
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


      // send email to admin user
      $user = \Drupal\user\Entity\User::load(1);
      $to = $user->getEmail();

      $mailBody[] = '<br><hr><br><strong>PQRSD Respondida:</strong>';
      $mailBody[] = 'Se dió respuesta a la PQRSD con número: <b>'.$node->get('field_pqrsd_numero_radicado')->getValue()[0]['value'].'</b><br><br>';
      $mailBody[] = '<div class="numero-radicado"><strong>Texto de respuesta:</strong><br>
      <blockquote>'.$node->get('field_pqrsd_respuesta')->getValue()[0]['value'].'</blockquote>
      </div>';

      $params['message'] = implode('<br>',$mailBody);

      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = true;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

      if($result['result'] !== true){
        \Drupal::messenger()->addError('Ocurrió un problema al enviar el correo.');
      }
    }

    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand(NULL, 'afterAsignCallback', ['Reloading page!']));
    return $response;

  }

}