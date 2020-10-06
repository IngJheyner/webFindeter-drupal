<?php

namespace Drupal\findeter_user_form_request\Validator;

/**
 * Class ValidatorEmail.
 *
 * @package Drupal\findeter_user_form_request\Validator
 */
class ValidatorEmail extends BaseValidator {

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
  }

}
