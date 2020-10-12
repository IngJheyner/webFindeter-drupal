<?php

namespace Drupal\findeter_user_form_request\Step;

use Drupal\findeter_user_form_request\Step\StepsEnum;
use Drupal\findeter_user_form_request\Button\StepFourNextButton;
use Drupal\findeter_user_form_request\Button\StepFourPreviousButton;

use Drupal\findeter_user_form_request\Validator\ValidatorNoAnonimous;

use Drupal\Core\Messenger\MessengerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StepFour.
 *
 * @package Drupal\findeter_user_form_request\Step
 */
class StepFour extends BaseStep {

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

    $this->step = StepsEnum::STEP_FOUR;
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
    return StepsEnum::STEP_FOUR;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepFourPreviousButton(),
      new StepFourNextButton(),
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
                      <li class="active">
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

    $values = $steps[1]->getValues();

    if($values['field_type_requester'] == 'anonimo'){

      $form['content-fields']['title'] = array(
        '#markup' => '<h2 class="text-center col-12">¿Usted desea ser notificado frente al trámite de esta solicitud?</h2>',
      );

      $form['content-fields']['field_contact_answer_channel_anonimous'] = [
        '#type'          => 'radios',
        '#validate'      => true,
        '#options'       => [0=>'Si',1=>'No'],
        '#default_value' => 1,
        '#attributes'    => ['id' => [
          'field-contact-answer-channel-anonimous',
          ]
        ],
      ];
    }

    $form['content-fields']['no-anonimuos'] = [
      '#type' => 'container',
      
      '#attributes' => ['id' => [
        'no-anonimous',
        ]
      ],
    ];
    
    $form['content-fields']['no-anonimuos']['title'] = [
      '#markup' => '<h2>Seleccione el canal por medio del cual le gustaría recibir la respuesta a su solicitud</h2>',
    ];
    $form['content-fields']['no-anonimuos']['field_contact_answer_channel'] = [
      '#type'    => 'radios',
      '#options' => $definitions['field_contact_answer_channel']->getSetting('allowed_values')
    ];

    $form['content-fields']['no-anonimuos']['field_authorization'] = [
      '#type'  => 'checkbox',
      '#title' => array_shift($definitions['field_authorization']->getSetting('allowed_values')),
    ];

    $form['content-fields']['no-anonimuos']['field_request_marketing'] = [
      '#type'  => 'checkbox',
      '#title' => array_shift($definitions['field_request_marketing']->getSetting('allowed_values')),
    ];

    //Populate values
    if(isset($steps[4])){
      foreach($steps[4]->values as $field=>$value){
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
      'field_contact_answer_channel',
      'field_authorization',
      'field_request_marketing',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'field_contact_answer_channel' => [
        new ValidatorNoAnonimous("Medio de contacto es requerido"),
      ],
      'field_authorization' => [
        new ValidatorNoAnonimous("Autorización es requerida"),
      ],
    ];
  }

}
