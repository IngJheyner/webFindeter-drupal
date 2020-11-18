<?php

namespace Drupal\findeter_pqrsd\Button;

use Drupal\findeter_pqrsd\Step\StepsEnum;

/**
 * Class StepZeroNextButton.
 *
 * @package Drupal\findeter_pqrsd\Button
 */
class StepZeroNextButton extends BaseButton {

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
    return[[
      '#type' => 'submit',
      '#value' => 'Peticiones',
      '#goto_step' => StepsEnum::STEP_ONE,
    ],[
      '#type' => 'submit',
      '#value' => 'Quejas',
      '#goto_step' => StepsEnum::STEP_ONE,
    ],[
      '#type' => 'submit',
      '#value' => 'Reclamos',
      '#goto_step' => StepsEnum::STEP_ONE,
    ],[
      '#type' => 'submit',
      '#value' => 'Sugerencias',
      '#goto_step' => StepsEnum::STEP_ONE,
    ],[
      '#type' => 'submit',
      '#value' => 'Denuncias',
      '#goto_step' => StepsEnum::STEP_ONE,
    ]];
  }

}
