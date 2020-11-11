<?php

namespace Drupal\findeter_user_form_request\Form;

use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\node\Entity\Node;

/**
 * Class AsignRequest.
 */
class AsignRequest extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'asign_request';
  }

  /**
   * Building all elements in the form
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {

    $config = $this->config('findeter.admin');

    $form_state->setCached(FALSE);

    // Nid param to store in the new references
    $form['node_id'] = array(
      '#type' => 'hidden',
      '#value' => $nid,
    );

    $form['text'] = [
      '#markup' => 'Seleccione el usuario a quien se reasignará el pedido'
    ];

    $rolesAllowed = explode(',',$config->get('roles'));

    $userStorage = \Drupal::entityTypeManager()->getStorage('user');
    $query = $userStorage->getQuery();
    $uids = $query
      ->condition('status', '1')
      ->condition('roles', $rolesAllowed,'IN')
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
    $form['#attached']['library'][] = 'findeter_user_form_request/global-scripts';

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
    $node->field_request_designatations[] = $user->getUsername().' | '.$user->id().' | '.date('j/m/Y H:i:s');
    $node->uid = $user->id(); 
    $node->save();

    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand(NULL, 'afterAsignCallback', ['Reloading page!']));

    return $response;

  }

}
