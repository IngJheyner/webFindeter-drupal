<?php

namespace Drupal\findeter_pqrsd\Button;

use Drupal\findeter_pqrsd\Step\StepsEnum;

/**
 * Class StepOneNextButton.
 *
 * @package Drupal\findeter_pqrsd\Button
 */
class StepOneNextButton extends BaseButton {

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
      '#goto_step' => StepsEnum::STEP_TWO,
    ];
  }

}
