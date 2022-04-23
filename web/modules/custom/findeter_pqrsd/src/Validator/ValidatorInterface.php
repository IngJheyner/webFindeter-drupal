<?php

namespace Drupal\findeter_pqrsd\Validator;

/**
 * Interface ValidatorInterface.
 *
 * @package Drupal\findeter_pqrsd\Validator
 */
interface ValidatorInterface {

  /**
   * Returns bool indicating if validation is ok.
   */
  public function validates($value,$allValues);

  /**
   * Returns error message.
   */
  public function getErrorMessage();

}
