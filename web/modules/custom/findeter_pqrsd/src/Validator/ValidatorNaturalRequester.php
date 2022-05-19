<?php


namespace Drupal\findeter_pqrsd\Validator;

/**
 * Class ValidatorRequired.
 *
 * @package Drupal\findeter_pqrsd\Validator
 */
class ValidatorNaturalRequester extends BaseValidator
{

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues){
    if($allValues['field_pqrsd_tipo_solicitante'] != 'juridica' && $allValues['field_pqrsd_tipo_solicitante'] != '2'){
      return is_array($value) ? !empty(array_filter($value)) : !empty($value);
    }else{
      return true;
    }
  }

}
