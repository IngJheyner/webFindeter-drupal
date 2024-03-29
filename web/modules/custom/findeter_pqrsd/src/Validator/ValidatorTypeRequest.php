<?php


namespace Drupal\findeter_pqrsd\Validator;

/**
 * Class ValidatorTypeRequest.
 *
 * @package Drupal\findeter_pqrsd\Validator
 */
class ValidatorTypeRequest extends BaseValidator
{

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues){
    
    if($allValues['field_pqrsd_tipo_radicado'] == 'Peticiones'){
      return is_array($value) ? !empty(array_filter($value)) : !empty($value);
    }else{
      return true;
    }
    
  }

}
