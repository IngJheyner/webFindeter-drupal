<?php

namespace Drupal\findeter_pqrsd\Validator;

/**
 * Class ValidatorRequired.
 *
 * @package Drupal\findeter_pqrsd\Validator
 */
class ValidatorRequired extends BaseValidator {

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues) {
    return is_array($value) ? !empty(array_filter($value)) : !empty($value);
  }

}
