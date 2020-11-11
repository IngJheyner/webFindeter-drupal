<?php


namespace Drupal\findeter_user_form_request\Validator;

/**
 * Class ValidatorRequired.
 *
 * @package Drupal\findeter_user_form_request\Validator
 */
class ValidatorNaturalRequester extends BaseValidator
{

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues){
    if($allValues['field_type_requester'] != 'juridica'){
      return is_array($value) ? !empty(array_filter($value)) : !empty($value);
    }else{
      return true;
    }
  }

}
