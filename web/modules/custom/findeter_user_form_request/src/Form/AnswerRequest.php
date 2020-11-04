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
use Drupal\file\Entity\File;

/**
 * Class AnswerRequest.
 */
class AnswerRequest extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'answer_request';
  }

  /**
   * Building all elements in the form
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {

    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'user_request');

    $form_state->setCached(FALSE);

    // Nid param to store in the new references
    $form['node_id'] = array(
      '#type' => 'hidden',
      '#value' => $nid,
    );

    $form['field_answer'] = [
      '#type'  => 'textarea',
      '#title' => 'Escriba la respuesta al requerimiento',
    ];
    
    $fileSettings = $definitions['field_answer_files']->getSettings();
    $form['field_answer_files'] = [
      '#type'            => 'managed_file',
      '#cardinality'     => 3,
      //'#upload_location' => 'public://'.$fileSettings['file_directory'],
      '#multiple'        => TRUE,
      '#title'           => $definitions['field_answer_files']->getLabel(),
      '#upload_validators' => [
        'file_validate_extensions' => [$fileSettings['file_extensions']],
      ]
    ];

    $form['field_answer_favor'] = [
      '#type'    => 'radios',
      '#title'   => $definitions['field_answer_favor']->getLabel(),
      '#options' => $definitions['field_answer_favor']->getSetting('allowed_values'),
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
    $node->field_answer[] = $formValues['field_answer'];
    $node->field_answer_favor[] = $formValues['field_answer_favor'];

    foreach($formValues['field_answer_files'] as $fid){
      $node->field_answer_files[] = $fid;

      $file = File::load($fid);
      $file->setPermanent();
      $file->save();
    }
            
    $node->save();

    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand(NULL, 'afterAsignCallback', ['Reloading page!']));

    return $response;

  }

}
