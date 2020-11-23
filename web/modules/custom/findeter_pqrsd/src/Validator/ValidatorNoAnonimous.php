<?php


namespace Drupal\findeter_pqrsd\Validator;

/**
 * Class ValidatorNoAnonimous.
 *
 * @package Drupal\findeter_pqrsd\Validator
 */
class ValidatorNoAnonimous extends BaseValidator
{

  /**
   * {@inheritdoc}
   */
  public function validates($value,$allValues){
    
    if($allValues['field_pqrsd_tipo_solicitante'] !== 'anonimo'){
      return is_array($value) ? !empty(array_filter($value)) : !empty($value);
    }else{
      if($allValues['field_qprsd_answer_channel_anonimous']==0){
        return is_array($value) ? !empty(array_filter($value)) : !empty($value);
      }else{
        return true;
      }
    }
    
  }

}
