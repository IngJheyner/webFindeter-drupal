<?php

namespace Drupal\findeter_user_form_request\Step;

use Drupal\findeter_user_form_request\Step\StepsEnum;
use Drupal\findeter_user_form_request\Button\StepOneNextButton;

use Drupal\findeter_user_form_request\Validator\ValidatorRequired;

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

    $form['step'] = array(
      '#markup' => '<ul class="steps-counter">
                      <li class="active">
                        <span class="number">1</span>
                        <span class="text">Radicar solicitud</span>
                      </li>
                      <li>
                        <span class="number">2</span>
                        <span class="text">Información básica del solicitante</span>
                      </li>
                      <li>
                        <span class="number">3</span>
                        <span class="text">Información del producto</span>
                      </li>
                      <li>
                        <span class="number">4</span>
                        <span class="text">Canal de respuesta</span>
                      </li>
                    </ul>',
    );

    $form['title'] = array(
      '#markup' => '<h2>Seleccione la manera como desea radicar su solicitud</h2>',
    );

    /*$fileSettings = $definitions['field_request_files']->getSettings();
    
    $form['field_request_files'] = [
      '#type'              => 'managed_file',
      '#cardinality'       => 3,
      '#multiple' => TRUE,
      '#title'             => $definitions['field_request_files']->getLabel(),
      '#upload_validators' => [
        'file_validate_extensions' => [$fileSettings['file_extensions']],
        'file_validate_size' => 20971520,
      ],
    ];

    /*$form['field_request_files'] = [
      '#type'              => 'managed_file',
      '#cardinality'       => 3,
      '#multiple' => TRUE,
      '#title'             => $definitions['field_request_files']->getLabel(),
      '#upload_validators' => [
        'file_validate_extensions' => [$fileSettings['file_extensions']],
      ],
    ];*/



    /*
    $form['entity_browser_wrapper'] = [
      '#type' => 'container',
      'widget' => [
          '#title' => t('Entity Browser'),
          '#field_name' => 'entity_browser_wrapper',
          '#field_parents' => [],
          '#required' => false,
          '#parents' => ['entity_browser_wrapper'],
          '#tree' => true,
          '#id' => 'edit-entity-browser-wrapper',
          '#type' => 'details',
          '#open' => true,
          'entity_browser' => [
              '#type' => 'entity_browser',
              '#entity_browser' => 'document_browser',
              '#cardinality' => 1,
              '#entity_browser_validators' => ['entity_type' => ['type' => 'media']],
              '#process' => [
                  ['\Drupal\entity_browser\Element\EntityBrowserElement', 'processEntityBrowser'],
                  ['\Drupal\entity_browser\Plugin\Field\FieldWidget\EntityReferenceBrowserWidget', 'processEntityBrowser'],
              ]
          ],
          'current' => [
              '#theme_wrappers' => ['container'],
              '#attributes' => [
                  'class' => 'entities-list',
              ],
              'items' => [],
          ],
      ],
  ];*/






    $form['field_type_request'] = [
      '#type'         => 'select',
      '#title'        => $definitions['field_type_request']->getLabel(),
      '#options'      => $definitions['field_type_request']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
    ];

    $form['field_type_requester'] = [
      '#type'         => 'select',
      '#title'        => $definitions['field_type_requester']->getLabel(),
      '#options'      => $definitions['field_type_requester']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
    ];

    $form['field_type_handicap'] = [
      '#type'         => 'select',
      '#title'        => $definitions['field_type_handicap']->getLabel(),
      '#options'      => $definitions['field_type_handicap']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
    ];

    $form['field_ethnic_group'] = [
      '#type'         => 'select',
      '#title'        => $definitions['field_ethnic_group']->getLabel(),
      '#options'      => $definitions['field_ethnic_group']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
    ];

    $form['field_preferential_attention'] = [
      '#type'         => 'select',
      '#title'        => $definitions['field_preferential_attention']->getLabel(),
      '#options'      => $definitions['field_preferential_attention']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
    ];

    $form['field_age_range'] = [
      '#type'         => 'select',
      '#title'        => $definitions['field_age_range']->getLabel(),
      '#options'      => $definitions['field_age_range']->getSetting('allowed_values'),
      '#empty_option' => '-Seleccione una opción-',
    ];

    //Populate values
    if(isset($steps[1])){
      foreach($steps[1]->values as $field=>$value){
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
        new ValidatorRequired("Tipo de petición es requerido"),
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
