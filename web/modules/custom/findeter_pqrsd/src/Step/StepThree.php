<?php

namespace Drupal\findeter_pqrsd\Step;

use Drupal\findeter_pqrsd\Step\StepsEnum;
use Drupal\findeter_pqrsd\Button\StepThreeNextButton;
use Drupal\findeter_pqrsd\Button\StepThreePreviousButton;

use Drupal\findeter_pqrsd\Validator\ValidatorRequired;

use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StepThree.
 *
 * @package Drupal\findeter_pqrsd\Step
 */
class StepThree extends BaseStep {

  // property to show messages
  private $messenger;

  /**
   * {@inheritdoc}
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
    $this->step = StepsEnum::STEP_THREE;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger')
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
    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'pqrsd');

    $formStep['title']['#markup'] = '<h2 class="text-center col-12">Información del producto</h2>';

    // start col 1
    $formStep['field_pqrsd_nombre_producto'] = [
      '#type'    => 'select',
      '#title'   => '<span class"required">*</span>'.$definitions['field_pqrsd_nombre_producto']->getLabel(),
      '#options' => $definitions['field_pqrsd_nombre_producto']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#prefix'       => '<div class="col">'
    ];

    $formStep['field_pqrsd_motivo'] = [
      '#type'    => 'select',
      '#title'   => $definitions['field_pqrsd_motivo']->getLabel(),
      '#options' => $definitions['field_pqrsd_motivo']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
    ];

    $formStep['field_pqrsd_otros'] = [
      '#type'       => 'textfield',
      '#title'      => $definitions['field_pqrsd_otros']->getLabel(),
      '#attributes' => ['placeholder'=>'Especifique cuál']
    ];

    $fileSettings = $definitions['field_pqrsd_archivo']->getSettings();

    // end col 1
    $formStep['field_pqrsd_archivo'] = [
      '#type'            => 'managed_file',
      '#cardinality'     => 3,
      '#multiple'        => TRUE,
      '#title'           => $definitions['field_pqrsd_archivo']->getLabel(),
      '#upload_validators' => [
        'file_validate_extensions' => [$fileSettings['file_extensions']],
        'file_validate_size'       => 20971520,
      ]
    ];

    // start/end col 1
    $formStep['field_pqrsd_descripcion'] = [
      '#type'        => 'textarea',
      '#maxlength'   => 3500,
      '#title'       => '<span class"required">*</span>'.$definitions['field_pqrsd_descripcion']->getLabel(),
      '#attributes'  => ['placeholder'=>'Escriba el detalle de su Petición, Queja, Reclamo, Sugerencia o Denuncia.','id'=>'edit-field-request-description'],
      '#description' => '<div>Puede ingresar hasta un máximo de 3500 caracteres. <br>Caracteres ingresados: <span class="counter-char">-</span>, máximo 3500 caracteres.</div>',
      '#prefix'      => '</div><div class="col">',
      '#suffix'      => '</div>'
    ];

    return $formStep;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'field_pqrsd_nombre_producto',
      'field_pqrsd_motivo',
      'field_pqrsd_otros',
      'field_pqrsd_archivo',
      'field_pqrsd_descripcion',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'field_pqrsd_nombre_producto' => [
        new ValidatorRequired("Nombre del producto es requerido"),
      ],
      'field_pqrsd_descripcion' => [
        new ValidatorRequired("Descripción de su solicitud es requerido"),
      ],
    ];
  }

}
