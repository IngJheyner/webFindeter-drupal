<?php

namespace Drupal\findeter_user_form_request\Step;

use Drupal\findeter_user_form_request\Step\StepsEnum;
use Drupal\findeter_user_form_request\Button\StepOneNextButton;

use Drupal\findeter_user_form_request\Validator\ValidatorRequired;
use Drupal\findeter_user_form_request\Validator\ValidatorTypeRequest;

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
 * @package Drupal\findeter_user_form_request\Step
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
    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'user_request');
    
    $formStep['title']['#markup'] = '<h2 class="text-center col-12">Seleccione la manera como desea radicar su solicitud</h2>';

    // start first col
    $formStep['field_type_requester'] = [
      '#type'         => 'select',
      '#title'        => '<span class"required">*</span>'.$definitions['field_type_requester']->getLabel(),
      '#options'      => $definitions['field_type_requester']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#prefix'       => '<div class="col">'
    ];

    $firstSubmit = $form_state->getTriggeringElement();
    if($firstSubmit['#value'] == 'Peticiones'){
      $formStep['field_type_request'] = [
        '#type'         => 'select',
        '#title'        => '<span class"required">*</span>'.$definitions['field_type_request']->getLabel(),
        '#options'      => $definitions['field_type_request']->getSetting('allowed_values'),
        '#empty_option' => '-Seleccione una opción-'
      ];
    }
    

    // end first col
    $formStep['field_type_handicap'] = [
      '#type'         => 'select',
      '#title'        => '<span class"required">*</span>'.$definitions['field_type_handicap']->getLabel(),
      '#options'      => $definitions['field_type_handicap']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#suffix'       => '</div>'
    ];

    // start second col
    $formStep['field_ethnic_group'] = [
      '#type'         => 'select',
      '#title'        => '<span class"required">*</span>'.$definitions['field_ethnic_group']->getLabel(),
      '#options'      => $definitions['field_ethnic_group']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#prefix'       => '<div class="col">'
    ];

    // end second col
    $formStep['field_preferential_attention'] = [
      '#type'         => 'select',
      '#title'        => '<span class"required">*</span>'.$definitions['field_preferential_attention']->getLabel(),
      '#options'      => $definitions['field_preferential_attention']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
      '#suffix'       => '</div>'
    ];

    $formStep['field_age_range'] = [
      '#type'         => 'select',
      '#title'        => '<span class"required">*</span>'.$definitions['field_age_range']->getLabel(),
      '#options'      => $definitions['field_age_range']->getSetting('allowed_values'),
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
      'field_type_request',
      'field_type_requester',
      'field_type_handicap',
      'field_ethnic_group',
      'field_preferential_attention',
      'field_age_range'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'field_type_request' => [
        new ValidatorTypeRequest("Tipo de petición es requerido"),
      ],
      'field_type_requester' => [
        new ValidatorRequired("Tipo de solicitante es requerido"),
      ],
      'field_type_handicap' => [
        new ValidatorRequired("Tipo de discapacidad es requerido"),
      ],
      'field_ethnic_group' => [
        new ValidatorRequired("Grupo étnico es requerido"),
      ],
      'field_preferential_attention' => [
        new ValidatorRequired("Atención preferencial es requerido"),
      ],
      'field_age_range' => [
        new ValidatorRequired("Rango de edad es requerido"),
      ]
    ];
  }

}
