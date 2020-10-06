<?php

namespace Drupal\findeter_user_form_request\Button;

use Drupal\findeter_user_form_request\Step\StepsEnum;

/**
 * Class StepThreeNextButton.
 *
 * @package Drupal\findeter_user_form_request\Button
 */
class StepThreeNextButton extends BaseButton {

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
      '#goto_step' => StepsEnum::STEP_FOUR,
    ];
  }

}
