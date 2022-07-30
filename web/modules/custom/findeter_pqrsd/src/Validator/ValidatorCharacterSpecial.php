<?php

namespace Drupal\findeter_pqrsd\Validator;

/**
 * Class ValidatorCharacterSpecial.
 *
 * @package Drupal\findeter_pqrsd\Validator
 */
class ValidatorCharacterSpecial extends BaseValidator {

  /**
   * Se valida caracteres especialees.
   */
  public function validates($value, $allValues) {
    if ($value != '') {
      return preg_match('/^[a-zA-Z\sñáéíóúÁÉÍÓÚÑ]+$/', $value);
    }
    else {
      return TRUE;
    }
  }

}
