<?php

namespace Drupal\findeter_pqrsd\Step;

use Drupal\findeter_pqrsd\Step\StepsEnum;
use Drupal\findeter_pqrsd\Button\StepFourNextButton;
use Drupal\findeter_pqrsd\Button\StepFourPreviousButton;

use Drupal\findeter_pqrsd\Validator\ValidatorNoAnonimous;

use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StepFour.
 *
 * @package Drupal\findeter_pqrsd\Step
 */
class StepFour extends BaseStep {

  // property to show messages
  private $messenger;

  /**
   * {@inheritdoc}
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
    $this->step = StepsEnum::STEP_FOUR;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
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
    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'pqrsd');

    // used as a container. It close with suffix attribute in the last element in this step
    $formStep['content_fields'] = [
      '#type'       => 'container',
      '#attributes' => ['class' => ['row']],
      '#prefix'     => '<div class="container">'
    ];

    $values = $steps[1]->getValues();

    // just for anonimous, take these fields
    if($values['field_pqrsd_tipo_solicitante'] == 'anonimo'){

      $formStep['title_anonimous']['#markup'] = '<h2 class="text-center col-12">¿Usted desea ser notificado frente al trámite de esta solicitud?</h2>';

      $formStep['field_qprsd_answer_channel_anonimous'] = [
        '#type'          => 'radios',
        '#validate'      => true,
        '#options'       => [0=>'Si',1=>'No'],
        '#default_value' => 1,
        '#attributes'    => ['id' => ['field-contact-answer-channel-anonimous']
        ],
      ];

    }
    
    $formStep['title'] = [
      '#markup' => '<h2 class="text-center col-12">Seleccione el canal por medio del cual le gustaría recibir la respuesta a su solicitud</h2>',
      '#prefix' => '<div id="no-anonimous">'
    ];
    $formStep['field_pqrsd_medio_respuesta'] = [
      '#type'    => 'radios',
      '#options' => $definitions['field_pqrsd_medio_respuesta']->getSetting('allowed_values')
    ];

    $formStep['field_pqrsd_autorizacion'] = [
      '#type'  => 'checkbox',
      '#title' => '<span class"required">*</span> '.array_shift($definitions['field_pqrsd_autorizacion']->getSetting('allowed_values')),
    ];

    $formStep['field_pqrsd_marketing'] = [
      '#type'  => 'checkbox',
      '#title' => array_shift($definitions['field_pqrsd_marketing']->getSetting('allowed_values')),
      '#suffix' => '</div>'
    ];

    return $formStep;
  }


  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'field_pqrsd_medio_respuesta',
      'field_pqrsd_autorizacion',
      'field_pqrsd_marketing',
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'field_pqrsd_medio_respuesta' => [
        new ValidatorNoAnonimous("Medio de contacto es requerido"),
      ],
      'field_pqrsd_autorizacion' => [
        new ValidatorNoAnonimous("Autorización es requerida"),
      ],
    ];
  }

}
