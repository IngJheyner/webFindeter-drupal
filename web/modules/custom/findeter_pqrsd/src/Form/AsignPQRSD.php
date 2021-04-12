<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\node\Entity\Node;

/**
 * Class AsignPQRSD.
 */
class AsignPQRSD extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'asign_pqrsd';
  }

  /**
   * Building all elements in the form
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL,$usr='') {

    $uidLogued = \Drupal::currentUser()->id();

    $config = $this->config('findeter_pqrsd.admin');

    $form_state->setCached(FALSE);

    // Nid param to store in the new references
    $form['node_id'] = array(
      '#type' => 'hidden',
      '#value' => $nid,
    );

    $form['text'] = [
      '#markup' => 'Seleccione el usuario a quien se reasignará el pedido.<br>Actualmente está asignado a: <b>'.$usr.'</b><br><br>'
    ];

    $rolesAllowed = explode(',',$config->get('roles'));

    $userStorage = \Drupal::entityTypeManager()->getStorage('user');
    $query = $userStorage->getQuery();
    $uids = $query
      ->condition('status', '1')
      ->condition('roles', $rolesAllowed,'IN')
      ->condition('uid',$uidLogued,'<>')
      ->execute();

    $users = $userStorage->loadMultiple($uids);
    $usersAsOptions = [];
    foreach($users as $uid=>$usr){
      $usersAsOptions[$uid] = $usr->getUsername();
    }

    $form['users'] = [
      '#type'    => 'radios',
      '#options' => $usersAsOptions
    ];


    // Submit with ajax event that after the operation, close the modal
    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Asignar',
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
    $user = \Drupal\user\Entity\User::load($formValues['users']);
    $node->field_pqrsd_asignaciones[] = $user->getUsername().' | '.$user->id().' | '.date('j/m/Y H:i:s');
    $node->uid = $user->id();
    $node->save();

    if($user->getEmail() !== ''){

      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'findeter_pqrsd';
      $key = 'asigned_pqrsd';
      $to = $user->getEmail();

      $userName = '';
      if(isset($node->get('field_pqrsd_primer_nombre')->getValue()[0]['value'])){
        $userName = $node->get('field_pqrsd_primer_nombre')->getValue()[0]['value'].' ';
      }
      if(isset($node->get('field_pqrsd_primer_apellido')->getValue()[0]['value'])){
        $userName .= $node->get('field_pqrsd_primer_apellido')->getValue()[0]['value'];
      }

      $mailBody[] = 'Hola '.$user->getUsername();
      $mailBody[] = 'Le informamos que se le asignó una PQRSD para que le dé respuesta:';
      $mailBody[] = '<div class="numero-radicado">
                      <b>Radicado: </b>'.$node->get('field_pqrsd_numero_radicado')->getValue()[0]['value'].'
                      <b>Cliente: </b>'.$userName.'
                      <b>Fecha registro: </b>'.date('d/m/Y H:m:i',$node->getCreatedTime()).'
                      <b>Fecha vencimiento respuesta: </b>'.$node->get('field_pqrsd_fecha_roja')->getValue()[0]['value'].'
                    </div>';
      $mailBody[] = 'Le invitamos a gestionar esta PQRSD, iniciando sesión en la página administrativa del sistema';
      $mailBody[] = '';
      $mailBody[] = 'Cordialmente,';
      $mailBody[] = 'Vicepresidencia comercial - Servicio al cliente';
      $mailBody[] = 'Findeter';

      $params['message'] = implode('<br>',$mailBody);

      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = true;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    }



    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand(NULL, 'afterAsignCallback', ['Reloading page!']));

    return $response;

  }

}
