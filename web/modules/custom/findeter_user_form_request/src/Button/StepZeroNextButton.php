<?php

namespace Drupal\findeter_user_form_request\Button;

use Drupal\findeter_user_form_request\Step\StepsEnum;

/**
 * Class StepZeroNextButton.
 *
 * @package Drupal\findeter_user_form_request\Button
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
