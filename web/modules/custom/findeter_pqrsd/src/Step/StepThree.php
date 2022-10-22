<?php

namespace Drupal\findeter_pqrsd\Step;

use Drupal\Core\Config\Schema\Undefined;
use Drupal\findeter_pqrsd\Step\StepsEnum;
use Drupal\findeter_pqrsd\Button\StepThreeNextButton;
use Drupal\findeter_pqrsd\Button\StepThreePreviousButton;

use Drupal\findeter_pqrsd\Validator\ValidatorRequired;

use Drupal\Core\Datetime\DrupalDateTime;

use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\findeter_pqrsd\Validator\ValidatorCharacterSpecial;

/**
 * Class StepThree.
 *
 * @package Drupal\findeter_pqrsd\Step
 */
class StepThree extends BaseStep {

  // property to show messages
  private $messenger;

  //step Zero Values
  private $valuesStepZero;

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

    $formStep['title']['#markup'] = '<h2 class="text-center mt-4 mb-5">Información del producto</h2>';

    /* ============================================
    Se muestra valores de producto para findeter o SMFC
    para SMFC solo si la peticion es una Queja o Reclamo
    esta cambia en su valor de indice mas no de item,
    SMFC numerico y Findeter caracter.
    =============================================== */
    $itemsProduct = $definitions['field_pqrsd_nombre_producto']->getSetting('allowed_values');

    //Obtenemos el valor de tipo de radicado
    $this->valuesStepZero = $steps[0]->getValues();

    $optionsProduct = [];
    $optionsSmfc = [];

    foreach ($itemsProduct as $key => $value) {
      if(is_numeric($key))
        $optionsSmfc[$key] = $value;
      else
        $optionsProduct[$key] = $value;
    }

    // start col 1
    $formStep['field_pqrsd_nombre_producto'] = [
      '#type'    => 'select',
      '#title'   => $definitions['field_pqrsd_nombre_producto']->getLabel(),
      '#options' => ($this->valuesStepZero['field_pqrsd_tipo_radicado'] == 'Quejas' ||
      $this->valuesStepZero['field_pqrsd_tipo_radicado'] == 'Reclamos') ? $optionsSmfc : $optionsProduct,
      '#empty_option' => '-Seleccione una opción-',
      '#prefix'       => '<div class="row mx-auto form-container"><div class="col-12 col-md-6 form-container-col">'
    ];

    /* ============================================
    Se muestra valores de motivos para findeter o SMFC
    para SMFC solo si la peticion es una Queja o Reclamo
    esta cambia en su valor de indice mas no de item,
    SMFC numerico y Findeter caracter.
    =============================================== */
    $itemReason = $definitions['field_pqrsd_motivo']->getSetting('allowed_values');

    $optionsReason = [];
    $optionsReasonSmfc = [];

    foreach ($itemReason as $key => $value) {
      if(is_numeric($key))
        $optionsReasonSmfc[$key] = $value;
      else
        $optionsReason[$key] = $value;
    }

    if ($this->valuesStepZero['field_pqrsd_tipo_radicado'] == 'Quejas' ||
    $this->valuesStepZero['field_pqrsd_tipo_radicado'] == 'Reclamos') {

      $formStep['container_reason'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['container-reason']],
      ];

      $formStep['container_reason']['field_pqrsd_motivo'] = [
        '#type'    => 'select2',
        '#title'   => $definitions['field_pqrsd_motivo']->getLabel(),
        '#options' => $optionsReasonSmfc,
        '#empty_option' => '-Seleccione una opción-',
        '#select2' => [
          'allowClear' => FALSE,
        ],
      ];

    }
    else {
      $formStep['field_pqrsd_motivo'] = [
        '#type'    => 'select',
        '#title'   => $definitions['field_pqrsd_motivo']->getLabel(),
        '#options' => $optionsReason,
        '#empty_option' => '-Seleccione una opción-',
        '#select2' => [
          'allowClear' => FALSE,
        ],
      ];
    }

    $formStep['field_pqrsd_otros'] = [
      '#type'       => 'textfield',
      '#title'      => $definitions['field_pqrsd_otros']->getLabel(),
      '#attributes' => ['placeholder' => 'Especifique cuál']
    ];

    /* ============================================
    Moficacion al cargue de anexo del radicado
    =============================================== */
    //Obtenemos el valor de numero ID
    $valuesStepTwo = $steps[2]->getValues();

    $fileSettings = $definitions['field_pqrsd_archivo']->getSettings();

    $dateTimestamp = strtotime(new DrupalDateTime());
    $date = \Drupal::service('date.formatter')->format($dateTimestamp, 'custom', 'Y-m-d');
    $time = str_replace(" ", "", (\Drupal::service('date.formatter')->format($dateTimestamp, 'custom', '\T\ H')));

    $time .= isset($valuesStepTwo['field_pqrsd_numero_id']) ? '---'.$valuesStepTwo['field_pqrsd_numero_id'] : '---NIT'.$valuesStepTwo['field_pqrsd_nit'];

    // End col 1.
    $formStep['field_pqrsd_archivo'] = [
      '#type'            => 'managed_file',
      '#cardinality'     => 3,
      '#multiple'        => TRUE,
      '#title'           => $definitions['field_pqrsd_archivo']->getLabel(),
      '#upload_location' => "private://pqrsd/{$this->valuesStepZero['field_pqrsd_tipo_radicado']}/$date/$time/",
      '#upload_validators' => [
        'file_validate_extensions' => [($this->valuesStepZero['field_pqrsd_tipo_radicado'] == 'Quejas' ||
        $this->valuesStepZero['field_pqrsd_tipo_radicado'] == 'Reclamos') ? \Drupal::service('api.smfc')->getExtFile() : $fileSettings['file_extensions']],
        'file_validate_size'       => [20971520],
      ]
    ];

    // Start/end col 1.
    $formStep['field_pqrsd_descripcion'] = [
      '#type'        => 'textarea',
      '#maxlength'   => 3500,
      '#title'       => $definitions['field_pqrsd_descripcion']->getLabel(),
      '#attributes'  => ['placeholder' => 'Escriba el detalle de su Petición, Queja, Reclamo, Sugerencia o Denuncia.','id'=>'edit-field-request-description'],
      '#description' => '<div>Puede ingresar hasta un máximo de 3500 caracteres. <br>Caracteres ingresados: <span class="counter-char">-</span>, máximo 3500 caracteres.</div>',
      '#prefix'      => '</div><div class="col-12 col-md-6 form-container-col">',
      '#suffix'      => '</div></div>'
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

    if ($this->valuesStepZero['field_pqrsd_tipo_radicado'] == 'Quejas' ||
    $this->valuesStepZero['field_pqrsd_tipo_radicado'] == 'Reclamos') {
      return [
        'field_pqrsd_nombre_producto' => [
          new ValidatorRequired("Nombre del producto es requerido"),
        ],
        'field_pqrsd_motivo' => [
          new ValidatorRequired("Motivo de su solicitud es requerido"),
        ],
        'field_pqrsd_descripcion' => [
          new ValidatorRequired("Descripción de su solicitud es requerido"),
          new ValidatorCharacterSpecial("Descripción: No se permite caracteres especiales ni números."),
        ]
      ];
    }
    else {
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

}
