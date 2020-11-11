<?php

namespace Drupal\findeter_pqrsd\Step;

use Drupal\findeter_pqrsd\Step\StepsEnum;
use Drupal\findeter_pqrsd\Button\StepOneNextButton;

use Drupal\findeter_pqrsd\Validator\ValidatorRequired;
use Drupal\findeter_pqrsd\Validator\ValidatorTypeRequest;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\entity_browser\Element;
use Drupal\entity_browser\Element\EntityBrowserElement;
use Drupal\entity_browser\Plugin\Field\FieldWidget\EntityReferenceBrowserWidget;



/**
 * Class StepOne.
 *
 * @package Drupal\findeter_pqrsd\Step
 */
class StepOne extends BaseStep {

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

    $this->step = StepsEnum::STEP_ONE;
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
    return StepsEnum::STEP_ONE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepOneNextButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($steps,$form,$form_state) {
    
    // Get data field definitions of content type
    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'pqrsd');
    
    $formStep['title']['#markup'] = '<h2 class="text-center col-12">Seleccione la manera como desea radicar su solicitud</h2>';

    // start first col
    $formStep['field_pqrsd_tipo_solicitante'] = [
      '#type'         => 'select',
      '#title'        => '<span class"required">*</span>'.$definitions['field_pqrsd_tipo_solicitante']->getLabel(),
      '#options'      => $definitions['field_pqrsd_tipo_solicitante']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#prefix'       => '<div class="col">'
    ];

    $firstSubmit = $form_state->getTriggeringElement();
    if($firstSubmit['#value'] == 'Peticiones'){
      $typeRequestValues = $definitions['field_pqrsd_tipo_peticion']->getSetting('allowed_values');
      // these options is just for administrator role
      unset($typeRequestValues['traslado']);
      unset($typeRequestValues['cargo']);
      unset($typeRequestValues['camaras']);
      unset($typeRequestValues['asesoria']);
      unset($typeRequestValues['irrespetuosa']);
      
      $formStep['field_pqrsd_tipo_peticion'] = [
        '#type'         => 'select',
        '#title'        => '<span class"required">*</span>'.$definitions['field_pqrsd_tipo_peticion']->getLabel(),
        '#options'      => $typeRequestValues,
        '#empty_option' => '-Seleccione una opción-'
      ];
    }
    

    // end first col
    $formStep['field_pqrsd_tipo_discapacidad'] = [
      '#type'         => 'select',
      '#title'        => $definitions['field_pqrsd_tipo_discapacidad']->getLabel(),
      '#options'      => $definitions['field_pqrsd_tipo_discapacidad']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#suffix'       => '</div>'
    ];

    // start second col
    $formStep['field_pqrsd_grupo_etnico'] = [
      '#type'         => 'select',
      '#title'        => $definitions['field_pqrsd_grupo_etnico']->getLabel(),
      '#options'      => $definitions['field_pqrsd_grupo_etnico']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#prefix'       => '<div class="col">'
    ];

    // end second col
    $formStep['field_pqrsd_preferencial'] = [
      '#type'         => 'select',
      '#title'        => $definitions['field_pqrsd_preferencial']->getLabel(),
      '#options'      => $definitions['field_pqrsd_preferencial']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#suffix'       => '</div>'
    ];

    $formStep['field_pqrsd_rango_edad'] = [
      '#type'         => 'select',
      '#title'        => '<span class"required">*</span>'.$definitions['field_pqrsd_rango_edad']->getLabel(),
      '#options'      => $definitions['field_pqrsd_rango_edad']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#prefix'       => '<div class="col-12">',
      '#suffix'       => '</div>'
    ];
    
    return $formStep;

  }


  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'field_pqrsd_tipo_peticion',
      'field_pqrsd_tipo_solicitante',
      'field_pqrsd_tipo_discapacidad',
      'field_pqrsd_grupo_etnico',
      'field_pqrsd_preferencial',
      'field_pqrsd_rango_edad'
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'field_pqrsd_tipo_peticion' => [
        new ValidatorTypeRequest("Tipo de petición es requerido"),
      ],
      'field_pqrsd_tipo_solicitante' => [
        new ValidatorRequired("Tipo de solicitante es requerido"),
      ],
      'field_pqrsd_rango_edad' => [
        new ValidatorRequired("Rango de edad es requerido"),
      ]
    ];
  }

}
