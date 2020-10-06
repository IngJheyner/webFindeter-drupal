<?php

namespace Drupal\findeter_user_form_request\Button;

use Drupal\findeter_user_form_request\Step\StepsEnum;

/**
 * Class StepTwoNextButton.
 *
 * @package Drupal\findeter_user_form_request\Button
 */
class StepTwoNextButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'next';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => 'Siguiente',
      '#goto_step' => StepsEnum::STEP_THREE,
    ];
  }

}
