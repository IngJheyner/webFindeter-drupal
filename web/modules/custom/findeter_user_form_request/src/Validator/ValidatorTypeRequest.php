<?php


namespace Drupal\findeter_user_form_request\Validator;

/**
 * Class ValidatorTypeRequest.
 *
 * @package Drupal\findeter_user_form_request\Validator
 */
class ValidatorTypeRequest extends BaseValidator
{

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues){
    
    if($allValues['field_type_form'] == 'Peticiones'){
      return is_array($value) ? !empty(array_filter($value)) : !empty($value);
    }else{
      return true;
    }
    
  }

}
