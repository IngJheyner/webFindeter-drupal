<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

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

    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'pqrsd');

    $form_state->setCached(FALSE);

    // Nid param to store in the new references
    $form['node_id'] = array(
      '#type' => 'hidden',
      '#value' => $nid,
    );

    $form['field_pqrsd_respuesta'] = [
      '#type'  => 'textarea',
      '#title' => 'Escriba la respuesta al requerimiento',
    ];

    $fileSettings = $definitions['field_pqrsd_respuesta_archivos']->getSettings();
    $form['field_pqrsd_respuesta_archivos'] = [
      '#type'            => 'managed_file',
      '#cardinality'     => 3,
      '#description'     => 'Puede registrar hasta 4 archivos',
      //'#upload_location' => 'public://'.$fileSettings['file_directory'],
      '#multiple'        => TRUE,
      '#title'           => $definitions['field_pqrsd_respuesta_archivos']->getLabel(),
      '#upload_validators' => [
        'file_validate_extensions' => [$fileSettings['file_extensions']],
      ]
    ];

    $form['field_pqrsd_respuesta_a_favor'] = [
      '#type'    => 'radios',
      '#title'   => $definitions['field_pqrsd_respuesta_a_favor']->getLabel(),
      '#options' => $definitions['field_pqrsd_respuesta_a_favor']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
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
    $formValues = $form_state->getValues();

    $node = Node::load($formValues['node_id']);
    $node->field_pqrsd_respuesta[] = $formValues['field_pqrsd_respuesta'];
    $node->field_pqrsd_respuesta_a_favor[] = $formValues['field_pqrsd_respuesta_a_favor'];
    $node->field_pqrsd_fecha_respuesta[] = date('Y-m-d\TH:i:s',strtotime('now'));

    foreach($formValues['field_pqrsd_respuesta_archivos'] as $fid){
      $node->field_pqrsd_respuesta_archivos[] = $fid;

      $file = File::load($fid);
      $file->setPermanent();
      $file->save();
    }

    $node->save();

    if(isset($node->get('field_pqrsd_email')->getValue()[0]['value']) &&
        $node->get('field_pqrsd_medio_respuesta')->getValue()[0]['value'] == 'email'){

      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'findeter_pqrsd';
      $key = 'answer_pqrsd';
      $to = $form_state->getValue('field_pqrsd_email');

      $mailBody[] = 'Reciba un cordial saludo de parte de Findeter';
      $mailBody[] = 'Le informamos que hemos dado respuesta la PQRSD con número: <b>'.$node->get('field_pqrsd_numero_radicado')->getValue()[0]['value'].'</b>';
      $mailBody[] = '<div class="numero-radicado">Estimado usuario: <br><br>
      '.$node->get('field_pqrsd_respuesta')->getValue()[0]['value'] .'
      </div>';

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


      // send email to admin user
      $user = \Drupal\user\Entity\User::load(1);
      $to = $user->getEmail();

      $mailBody[] = 'PQRSD Respondida:';
      $mailBody[] = 'Se dió respuesta a la PQRSD con número: <b>'.$node->get('field_pqrsd_numero_radicado')->getValue()[0]['value'].'</b>';
      $mailBody[] = '<div class="numero-radicado">Texto de respuesta: <br><br>
      '.$node->get('field_pqrsd_respuesta')->getValue()[0]['value'] .'
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
