<?php


namespace Drupal\findeter_user_form_request\Validator;

/**
 * Class ValidatorNoAnonimous.
 *
 * @package Drupal\findeter_user_form_request\Validator
 */
class ValidatorNoAnonimous extends BaseValidator
{

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues){
    
    if($allValues['field_type_requester'] !== 'anonimo'){
      return is_array($value) ? !empty(array_filter($value)) : !empty($value);
    }else{
      if($allValues['field_contact_answer_channel_anonimous']==0){
        return is_array($value) ? !empty(array_filter($value)) : !empty($value);
      }else{
        return true;
      }
    }
    
  }

}
