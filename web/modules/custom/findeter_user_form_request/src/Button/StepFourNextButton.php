<?php

namespace Drupal\findeter_user_form_request\Button;

use Drupal\findeter_user_form_request\Step\StepsEnum;

/**
 * Class StepFourNextButton.
 *
 * @package Drupal\findeter_user_form_request\Button
 */
class StepFourNextButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'finish';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => 'Enviar solicitud',
      '#goto_step' => StepsEnum::STEP_FINALIZE,
      '#submit_handler' => 'submitValues',
    ];
  }

}
