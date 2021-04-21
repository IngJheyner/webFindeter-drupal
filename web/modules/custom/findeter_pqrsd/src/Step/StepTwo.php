<?php

namespace Drupal\findeter_pqrsd\Step;

use Drupal\findeter_pqrsd\Step\StepsEnum;
use Drupal\findeter_pqrsd\Button\StepTwoNextButton;
use Drupal\findeter_pqrsd\Button\StepTwoPreviousButton;

use Drupal\findeter_pqrsd\Validator\ValidatorRequired;
use Drupal\findeter_pqrsd\Validator\ValidatorEmail;
use Drupal\findeter_pqrsd\Validator\ValidatorNumber;
use Drupal\findeter_pqrsd\Validator\ValidatorLegalRequester;
use Drupal\findeter_pqrsd\Validator\ValidatorNaturalRequester;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StepTwo.
 *
 * @package Drupal\findeter_pqrsd\Step
 */
class StepTwo extends BaseStep {

  // property to show messages
  private $messenger;

  /**
   * {@inheritdoc}
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
    $this->step = StepsEnum::STEP_TWO;
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
    return StepsEnum::STEP_TWO;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepTwoPreviousButton(),
      new StepTwoNextButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($steps,$form,$form_state) {

    // Get the definitions
    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'pqrsd');

    //values previous step
    $values = $steps[1]->getValues();

    if($values['field_pqrsd_tipo_solicitante'] == 'juridica'){
      $formStep['title']['#markup'] = '<h2 class="text-center col-12">Información básica: Persona jurídica</h2>';

      // start col 1
      $formStep['field_pqrsd_nit'] = [
        '#type'       => 'textfield',
        '#title'      => '<span class"required">*</span>'.$definitions['field_pqrsd_nit']->getLabel(),
        //'#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_pqrsd_nit']->getLabel())],
        '#attributes' => ['placeholder'=>'Ej: 2321133'],
        '#prefix'     => '<div class="col">'
      ];

      $formStep['field_pqrsd_razon_social'] = [
        '#type'       => 'textfield',
        '#title'      => '<span class"required">*</span>'.$definitions['field_pqrsd_razon_social']->getLabel(),
        //'#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_pqrsd_razon_social']->getLabel())]
        '#attributes' => ['placeholder'=>'Ej: Empresa S.A.S'],
      ];

      $formStep['field_pqrsd_tipo_empresa'] = [
        '#type'         => 'select',
        '#title'        => '<span class"required">*</span>'.$definitions['field_pqrsd_tipo_empresa']->getLabel(),
        '#options'      => $definitions['field_pqrsd_tipo_empresa']->getSetting('allowed_values'),
        '#empty_option' => '-Seleccione una opción-',
      ];

    }else{
      $formStep['title']['#markup'] = '<h2 class="text-center col-12">Información básica: Persona '.$values['field_type_requester'].'</h2>';

      // start col 1
      $formStep['field_pqrsd_numero_id'] = [
        '#type'       => 'textfield',
        '#title'      => '<span class"required">*</span>'.$definitions['field_pqrsd_numero_id']->getLabel(),
        //'#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_pqrsd_numero_id']->getLabel())],
        '#attributes' => ['placeholder'=>'Ej: 2321133'],
        '#prefix'     => '<div class="col">'
      ];

      $formStep['field_pqrsd_tipo_documento'] = [
        '#type'    => 'select',
        '#title'   => $definitions['field_pqrsd_tipo_documento']->getLabel(),
        '#options' => $definitions['field_pqrsd_tipo_documento']->getSetting('allowed_values')
      ];

    }

    $formStep['field_pqrsd_primer_nombre'] = [
      '#type'       => 'textfield',
      '#title'      => '<span class"required">*</span>'.$definitions['field_pqrsd_primer_nombre']->getLabel(),
      '#attributes' => ['placeholder'=>'Ej: Juan']
    ];

    $formStep['field_pqrsd_segundo_nombre'] = [
      '#type'       => 'textfield',
      '#title'      => $definitions['field_pqrsd_segundo_nombre']->getLabel(),
      '#attributes' => ['placeholder'=>'Ej: Carlos']
    ];

    $formStep['field_pqrsd_primer_apellido'] = [
      '#type'       => 'textfield',
      '#title'      => '<span class"required">*</span>'.$definitions['field_pqrsd_primer_apellido']->getLabel(),
      '#attributes' => ['placeholder'=>'Ej: Pedraza']
    ];

    // end col 1
    $formStep['field_pqrsd_segundo_apellido'] = [
      '#type'       => 'textfield',
      '#title'      => $definitions['field_pqrsd_segundo_apellido']->getLabel(),
      '#attributes' => ['placeholder'=>'Ej: Ortega'],
      '#suffix'       => '</div>'
    ];

    if($values['field_pqrsd_tipo_solicitante'] == 'juridica'){
      $formStep['field_pqrsd_primer_nombre']['#title'] .= ' del representante legal';
      $formStep['field_pqrsd_segundo_nombre']['#title'] .= ' del representante legal';
      $formStep['field_pqrsd_primer_apellido']['#title'] .= ' del representante legal';
      $formStep['field_pqrsd_segundo_apellido']['#title'] .= ' del representante legal';
    }

    // start col 2
    $formStep['field_pqrsd_direccion'] = [
      '#type'       => 'textfield',
      '#title'      => '<span class"required">*</span>'.$definitions['field_pqrsd_direccion']->getLabel(),
      '#attributes' => ['placeholder'=>'Ej: carrera 2 No 32 - 15'],
      '#prefix'     => '<div class="col">'
    ];

    $deparmentOptions = getTaxonomyTermsForm(0);

    $formStep['field_pqrsd_departamento'] = [
      '#type'    => 'select',
      '#title'   => '<span class"required">*</span>'.$definitions['field_pqrsd_departamento']->getLabel(),
      '#options' => $deparmentOptions,
      '#source'  => 'steps',
      '#ajax'    => [
        'callback'  => 'callBackDeparment', 
        'event'     => 'change',
        'progress'  => [
          'message' => 'Recuperando municipios...',
        ],
      ],
      '#empty_option'   => '-Seleccione una opción-',
    ];

    $departmentValue = false;
    if($form_state->getValue('field_pqrsd_departamento') != ''){
      $departmentValue = $form_state->getValue('field_pqrsd_departamento');
    }elseif(isset($steps[2]->values['field_pqrsd_departamento'])){
      $departmentValue = $steps[2]->values['field_pqrsd_departamento'];
    }

    $formStep['field_pqrsd_municipio'] = [
      '#type'      => 'select',
      '#title'     => '<span class"required">*</span>'.$definitions['field_pqrsd_municipio']->getLabel(),
      '#prefix'    => '<div id="output-municipalities">',
      '#suffix'    => '</div>',
      '#empty_option' => '-Seleccione una opción-',
    ];

    if ($departmentValue) {
      $formStep['field_pqrsd_municipio']['#options'] = getTaxonomyTermsForm($departmentValue);
    }

    $formStep['field_pqrsd_telefono'] = [
      '#type'       => 'textfield',
      '#title'      => '<span class"required">*</span>'.$definitions['field_pqrsd_telefono']->getLabel(),
      '#attributes' => ['placeholder'=>'Ej: 5435455']
    ];

    $formStep['field_pqrsd_fax'] = [
      '#type'       => 'textfield',
      '#title'      => $definitions['field_pqrsd_fax']->getLabel(),
      '#attributes' => ['placeholder'=>'Ej: 4354555']
    ];
    
    // end col 2
    $formStep['field_pqrsd_email'] = [
      '#type'       => 'email',
      '#title'      => '<span class"required">*</span>'.$definitions['field_pqrsd_email']->getLabel(),
      '#attributes' => ['placeholder'=>'Ej: correo@gmail.com'],
      '#suffix'     => '</div>'
    ];
    
    return $formStep;
  }



  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'field_pqrsd_nit',
      'field_pqrsd_razon_social',
      'field_pqrsd_tipo_empresa',
      'field_pqrsd_numero_id',
      'field_pqrsd_tipo_documento',
      'field_pqrsd_primer_nombre',
      'field_pqrsd_segundo_nombre',
      'field_pqrsd_primer_apellido',
      'field_pqrsd_segundo_apellido',
      'field_pqrsd_direccion',
      'field_pqrsd_departamento',
      'field_pqrsd_municipio',
      'field_pqrsd_telefono',
      'field_pqrsd_fax',
      'field_pqrsd_email',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'field_pqrsd_nit' => [
        new ValidatorLegalRequester("Nit es requerido"),
      ],
      'field_pqrsd_razon_social' => [
        new ValidatorLegalRequester("Razón social es requerido"),
      ],
      'field_pqrsd_tipo_empresa' => [
        new ValidatorLegalRequester("Tipo de empresa es requerido"),
      ],
      'field_pqrsd_numero_id' => [
        new ValidatorNaturalRequester("Número identificación es requerido"),
        new ValidatorNumber("Número identificación solo permite números"),
      ],
      'field_pqrsd_tipo_documento' => [
        new ValidatorNaturalRequester("Tipo documento ID es requerido"),
      ],
      'field_pqrsd_primer_nombre' => [
        new ValidatorRequired("Primer nombre es requerido"),
      ],
      'field_pqrsd_primer_apellido' => [
        new ValidatorRequired("Primer apellido es requerido"),
      ],
      'field_pqrsd_direccion' => [
        new ValidatorRequired("Dirección correspondencia es requerido"),
      ],
      'field_pqrsd_departamento' => [
        new ValidatorRequired("Departamento es requerido"),
      ],
      'field_pqrsd_municipio' => [
        new ValidatorRequired("Municipio es requerido"),
      ],
      'field_pqrsd_fax' => [
        new ValidatorNumber("Fax solo permite números"),
      ],
      'field_pqrsd_telefono' => [
        new ValidatorRequired("Teléfono de contacto es requerido"),
        new ValidatorNumber("Teléfono de contacto solo permite números"),
      ],
      'field_pqrsd_email' => [
        new ValidatorRequired("Correo electrónico es requerido "),
        new ValidatorEmail("Correo electrónico inválido"),
      ],
    ];
  }

}
