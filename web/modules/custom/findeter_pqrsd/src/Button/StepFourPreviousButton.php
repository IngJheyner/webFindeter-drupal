<?php

namespace Drupal\findeter_pqrsd\Button;

use Drupal\findeter_pqrsd\Step\StepsEnum;

/**
 * Class StepFourPreviousButton.
 *
 * @package Drupal\findeter_pqrsd\Button
 */
class StepFourPreviousButton extends BaseButton {

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
      '#goto_step' => StepsEnum::STEP_THREE,
      '#skip_validation' => TRUE,
    ];
  }
  
}
