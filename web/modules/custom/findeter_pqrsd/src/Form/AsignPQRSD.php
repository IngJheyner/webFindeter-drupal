<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\user\Entity\User;

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
   * Building all elements in the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL, $usr = '') {

    $uidLogued = \Drupal::currentUser()->id();

    $config = $this->config('findeter_pqrsd.admin');

    $form_state->setCached(FALSE);

    // Nid param to store in the new references.
    $form['node_id'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    $form['text'] = [
      '#markup' => "Seleccione el usuario a quien se reasignará el pedido.<br> Actualmente está asignado a: <b> $usr </b> <br> <br>",
    ];

    $rolesAllowed = explode(',', $config->get('roles'));

    $userStorage = \Drupal::entityTypeManager()->getStorage('user');
    $query = $userStorage->getQuery();
    $uids = $query
      ->condition('status', '1')
      ->condition('roles',$rolesAllowed, 'IN')
      ->condition('uid',$uidLogued, '<>')
      ->execute();

    $users = $userStorage->loadMultiple($uids);
    $usersAsOptions = [];
    foreach($users as $uid => $usr) {
      $usersAsOptions[$uid] = $usr->getAccountName();
    }

    $form['users'] = [
      '#type'    => 'radios',
      '#options' => $usersAsOptions
    ];

    // Submit with ajax event that after the operation, close the modal.
    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => 'Asignar',
      '#ajax'  => [
        'callback' => '::ajaxCloseModal',
        'event'    => 'click',
        'progress' => [
          'type' => 'bar',
          'message' => 'Asignando al nuevo usuario..',
        ],
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
  }

  /**
   * Submit funtion (also close the modal window).
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Funcion que se llama para el envio de asignacion del nuevo usuario.
   */
  public function ajaxCloseModal(array &$form, FormStateInterface $form_state) {

    $formValues = $form_state->getValues();

    $node = Node::load($formValues['node_id']);
    $user = User::load($formValues['users']);
    $node->field_pqrsd_asignaciones[] = $user->getAccountName().' | '.$user->id().' | '.date('j/m/Y H:i:s');
    $node->uid = $user->id();
    $node->save();

    // Se envia la notificacion al nuevo usuario.
    if ($user->getEmail() !== '') {
      sendNotificationAsign($user, $node);
    }

    $response = new AjaxResponse();
    $url = Url::fromRoute('view.pqrsd.page_1');
    $response->addCommand(new RedirectCommand($url->toString()));

    return $response;

  }

}
