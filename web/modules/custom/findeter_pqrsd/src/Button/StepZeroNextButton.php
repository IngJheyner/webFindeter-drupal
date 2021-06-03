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
      '#attributes' => ['class'=>['col-xs-6', 'col-md-2', 'mb-3', 'mb-md-0', 'mx-3']],
      '#goto_step' => StepsEnum::STEP_ONE,      
    ],[
      '#type' => 'submit',
      '#value' => 'Quejas',
      '#attributes' => ['class'=>['col-xs-6', 'col-md-2', 'mb-3', 'mb-md-0', 'mx-3']],
      '#goto_step' => StepsEnum::STEP_ONE,
    ],[
      '#type' => 'submit',
      '#value' => 'Reclamos',
      '#attributes' => ['class'=>['col-xs-6', 'col-md-2', 'mb-3', 'mb-md-0', 'mx-3']],
      '#goto_step' => StepsEnum::STEP_ONE,
    ],[
      '#type' => 'submit',
      '#value' => 'Sugerencias',
      '#attributes' => ['class'=>['col-xs-6', 'col-md-2', 'mb-3', 'mb-md-0', 'mx-3']],
      '#goto_step' => StepsEnum::STEP_ONE,
    ],[
      '#type' => 'submit',
      '#value' => 'Denuncias',
      '#attributes' => ['class'=>['col-xs-6', 'col-md-2', 'mb-3', 'mb-md-0', 'mx-3']],
      '#goto_step' => StepsEnum::STEP_ONE,
    ]];
  }

}
