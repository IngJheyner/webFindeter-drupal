<?php

namespace Drupal\findeter_user_form_request\Validator;

/**
 * Interface ValidatorInterface.
 *
 * @package Drupal\findeter_user_form_request\Validator
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
