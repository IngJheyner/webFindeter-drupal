<?php

namespace Drupal\findeter_user_form_request\Validator;

/**
 * Class ValidatorNumber.
 *
 * @package Drupal\findeter_user_form_request\Validator
 */
class ValidatorNumber extends BaseValidator {

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues) {
    if($value != ''){
      return filter_var($value, FILTER_VALIDATE_INT);
    }else{
      return true;
    }
  }

}
