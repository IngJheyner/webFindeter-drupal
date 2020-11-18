<?php

namespace Drupal\findeter_pqrsd\Validator;

/**
 * Class ValidatorEmail.
 *
 * @package Drupal\findeter_pqrsd\Validator
 */
class ValidatorEmail extends BaseValidator {

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
  }

}
