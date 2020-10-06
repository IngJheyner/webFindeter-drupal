<?php

namespace Drupal\findeter_user_form_request\Validator;

/**
 * Class ValidatorRequired.
 *
 * @package Drupal\findeter_user_form_request\Validator
 */
class ValidatorRequired extends BaseValidator {

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues) {
    return is_array($value) ? !empty(array_filter($value)) : !empty($value);
  }

}
