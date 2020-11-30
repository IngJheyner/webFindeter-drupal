<?php

namespace Drupal\findeter_pqrsd\Validator;

/**
 * Class ValidatorNumber.
 *
 * @package Drupal\findeter_pqrsd\Validator
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
