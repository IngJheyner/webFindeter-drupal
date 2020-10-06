<?php

namespace Drupal\findeter_user_form_request\Button;

use Drupal\findeter_user_form_request\Step\StepsEnum;

/**
 * Class StepThreePreviousButton.
 *
 * @package Drupal\findeter_user_form_request\Button
 */
class StepThreePreviousButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'previous';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => 'Volver',
      '#goto_step' => StepsEnum::STEP_TWO,
      '#skip_validation' => TRUE,
    ];
  }
  
}
