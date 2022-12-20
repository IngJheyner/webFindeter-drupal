<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\node\Entity\Node;

/**
 * Class TransferPQRSD.
 */
class TransferPQRSD extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'transfer_pqrsd';
  }

  /**
   * Building all elements in the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {

    $config = $this->config('findeter_pqrsd.admin');

    $form_state->setCached(FALSE);

    // Nid param to store in the new references.
    $form['node_id'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    // Entidad de traslado.
    $form['entities'] = [
      '#type'       => 'textfield',
      '#title'      => 'Ingrese el nombre de la entidad a donde se va a transferir.',
      '#attributes' => ['placeholder' => 'Diligencie la entidad'],
      '#required'   => TRUE
    ];

    // Start col 2.
    $form['email'] = [
      '#type'       => 'textfield',
      '#title'      => 'Ingrese el correo a donde transferir',
      '#attributes' => ['placeholder' => 'Diligencie el correo'],
      '#required'   => TRUE
    ];

    $form['comment'] = [
      '#type'     => 'textarea',
      '#title'    => 'Comentario',
      '#required' => TRUE
    ];

    // Submit with ajax event that after the operation, close the modal.
    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Transferir',
      '#ajax'  => [
        'callback' => '::ajaxCloseModal',
        'event'    => 'click',
      ],
    ];

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'findeter_pqrsd/global-scripts';

    $form_state->setRebuild();

    return $form;
  }

  /**
   * To validate values of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);

    if (!filter_var($form_state->getValue('email'), FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('email', 'Debe ingresar una dirección de correo válido');
    }
  }

  /**
   * Submit funtion (also close the modal window).
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Ajax close modal send email.
   */
  public function ajaxCloseModal(array &$form, FormStateInterface $form_state) {

    $response = new AjaxResponse();

    $mailBody[] = '<p>Reciba un cordial saludo de parte de Findeter.</p><br>';

    $formValues = $form_state->getValues();

    $node = Node::load($formValues['node_id']);

    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'findeter_pqrsd';
    $key = 'transfer_pqrsd';
    $to = $formValues['email'];

    $nameRadicador[] = $node->get('field_pqrsd_primer_nombre')->getString();
    $nameRadicador[] = $node->get('field_pqrsd_segundo_nombre')->getString();
    $nameRadicador[] = $node->get('field_pqrsd_segundo_apellido')->getString();

    $mailBody[] = "<p>De conformidad con lo establecido en la ley 1755 de 2015, de manera atenta damos traslado por competencia a la PQRSD radicada en Findeter con el número: <strong>{$node->get('field_pqrsd_numero_radicado')->getString()}</strong></p>";
    $mailBody[] = '<p>Agradecemos su acostumbrada colaboración, para dar continuidad al trámite correspondiente.</p><br>';
    $mailBody[] = '<hr>';
    $mailBody[] = 'Cordialmente,';
    $mailBody[] = 'Vicepresidencia comercial - Servicio al cliente Findeter';
    $mailBody[] = '<a href="https://www.findeter.gov.co" target="_blank"><img onerror="this.remove();" alt="Findeter" src="https://www.findeter.gov.co/sites/default/files/webfinde/images/encabezado/logo.png" width="300" height="150"></a>';

    $params['message'] = implode('<br>', $mailBody);

    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = TRUE;

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    if ($result['result'] == TRUE) {
      $node = Node::load($formValues['node_id']);
      $node->field_pqrsd_respuesta[] = "PQRSD Trasladada a: {$formValues['entities']} ({$formValues['email']})";
      $node->field_pqrsd_comentario_traslado[] = $formValues['comment'];
      $node->field_entities_transfer_pqrsd[] = "{$formValues['entities']} ({$formValues['email']})";
      // $node->uid = 0;
      $node->save();

      $response->addCommand(new InvokeCommand(NULL, 'afterAsignCallback', ['Reloading page!']));
    }
    else {
      \Drupal::messenger()->addError('Ocurrió un problema al enviar el correo.');
      $response->addCommand(new CloseModalDialogCommand());
    }

    return $response;

  }

}
