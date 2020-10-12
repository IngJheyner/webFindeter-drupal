<?php

namespace Drupal\findeter_user_form_request\Step;

use Drupal\findeter_user_form_request\Step\StepsEnum;
use Drupal\findeter_user_form_request\Button\StepThreeNextButton;
use Drupal\findeter_user_form_request\Button\StepThreePreviousButton;

use Drupal\findeter_user_form_request\Validator\ValidatorRequired;

use Drupal\Core\Messenger\MessengerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StepThree.
 *
 * @package Drupal\findeter_user_form_request\Step
 */
class StepThree extends BaseStep {

  // property to show messages
  private $messenger;

  // to manage logs entry
  private $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(MessengerInterface $messenger,LoggerInterface $logger) {
    $this->messenger = $messenger;
    $this->logger = $logger;

    $this->step = StepsEnum::STEP_THREE;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('logger.factory')->get('multiStep')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_THREE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepThreePreviousButton(),
      new StepThreeNextButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($steps,$form,$form_state) {

    // Get the definitions
    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'user_request');

    $form['step'] = array(
      '#markup' => '<ul class="steps-counter">
                      <li class="active">
                        <span class="number">1</span>
                        <span class="text">Radicar solicitud</span>
                      </li>
                      <li class="active">
                        <span class="number">2</span>
                        <span class="text">Información básica del solicitante</span>
                      </li>
                      <li class="active">
                        <span class="number">3</span>
                        <span class="text">Información del producto</span>
                      </li>
                      <li>
                        <span class="number">4</span>
                        <span class="text">Canal de respuesta</span>
                      </li>
                    </ul>',
    );

    $form['content-fields'] = [
      '#type'       => 'container',
      '#attributes' => ['class' => ['row']],
      '#prefix'     => '<div class="container">'
    ];


    $form['content-fields']['title'] = array(
      '#markup' => '<h2 class="text-center col-12">Información del producto</h2>',
    );

    $form['content-fields']['col1'] = [
      '#type'       => 'container',
      '#attributes' => ['class' => ['col']],
    ];

    $form['content-fields']['col1']['field_product_name'] = [
      '#type'    => 'select',
      '#title'   => '<span class"required">*</span>'.$definitions['field_product_name']->getLabel(),
      '#options' => $definitions['field_product_name']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
    ];

    $form['content-fields']['col1']['field_request_reason'] = [
      '#type'    => 'select',
      '#title'   => $definitions['field_request_reason']->getLabel(),
      '#options' => $definitions['field_request_reason']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
    ];

    $form['content-fields']['col1']['field_request_other'] = [
      '#type'       => 'textfield',
      '#title'      => $definitions['field_request_other']->getLabel(),
      '#attributes' => ['placeholder'=>'Especifique cuál']
    ];

    $fileSettings = $definitions['field_request_files']->getSettings();
    $form['content-fields']['col1']['field_request_files'] = [
      '#type'            => 'managed_file',
      '#cardinality'     => 3,
      '#upload_location' => 'public://'.$fileSettings['file_directory'],
      '#multiple'        => TRUE,
      '#title'           => $definitions['field_request_files']->getLabel(),
      '#upload_validators' => [
        'file_validate_extensions' => [$fileSettings['file_extensions']],
        'file_validate_size'       => 20971520,
      ],
    ];

    $form['content-fields']['col2'] = [
      '#type'       => 'container',
      '#attributes' => ['class' => ['col']],
    ];


    $form['content-fields']['col2']['field_request_description'] = [
      '#type'        => 'textarea',
      '#maxlength'   => 4000,
      '#title'       => '<span class"required">*</span>'.$definitions['field_request_description']->getLabel(),
      '#attributes'  => ['placeholder'=>'Escriba el detalle de su Petición, Queja, Reclamo, Sugerencia o Denuncia.','id'=>'edit-field-request-description'],
      '#description' => '<div>Puede ingresar hasta un máximo de 4000 caracteres. <br>Caracteres ingresados: <span class="counter-char">-</span>, máximo 4000 caracteres.</div>'  
    ];

    //Populate values
    if(isset($steps[3])){
      foreach($steps[3]->values as $field=>$value){
        if(isset($form[$field])){
          $form[$field]['#default_value'] = $value;
        }
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'field_product_name',
      'field_request_reason',
      'field_request_other',
      'field_request_files',
      'field_request_description',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'field_product_name' => [
        new ValidatorRequired("Nombre del producto es requerido"),
      ],
      'field_request_description' => [
        new ValidatorRequired("Descripción de su solicitud"),
      ],
    ];
  }

}
