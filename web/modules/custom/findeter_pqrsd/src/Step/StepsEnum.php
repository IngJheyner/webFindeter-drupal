<?php

namespace Drupal\findeter_pqrsd\Step;

/**
 * Class StepsEnum.
 *
 * @package Drupal\findeter_pqrsd\Step
 */
abstract class StepsEnum {

  /**
   * Steps used in form.
   */
  const STEP_ZERO = 0;
  const STEP_ONE = 1;
  const STEP_TWO = 2;
  const STEP_THREE = 3;
  const STEP_FOUR = 4;
  const STEP_FINALIZE = 6;

  /**
   * Return steps associative array.
   *
   * @return array
   *   Associative array of steps.
   */
  public static function toArray() {
    return [
      self::STEP_ZERO => 'step-zero',
      self::STEP_ONE => 'step-one',
      self::STEP_TWO => 'step-two',
      self::STEP_THREE => 'step-three',
      self::STEP_FOUR => 'step-four',
      self::STEP_FINALIZE => 'step-finalize',
    ];
  }

  /**
   * Map steps to it's class.
   *
   * @param int $step
   *   Step number.
   *
   * @return bool
   *   Return true if exist.
   */
  public static function map($step) {
    $map = [
      self::STEP_ZERO => 'Drupal\\findeter_pqrsd\\Step\\StepZero',
      self::STEP_ONE => 'Drupal\\findeter_pqrsd\\Step\\StepOne',
      self::STEP_TWO => 'Drupal\\findeter_pqrsd\\Step\\StepTwo',
      self::STEP_THREE => 'Drupal\\findeter_pqrsd\\Step\\StepThree',
      self::STEP_FOUR => 'Drupal\\findeter_pqrsd\\Step\\StepFour',
      self::STEP_FINALIZE => 'Drupal\\findeter_pqrsd\\Step\\StepFinalize',
    ];

    return isset($map[$step]) ? $map[$step] : FALSE;
  }

}
