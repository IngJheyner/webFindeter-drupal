<?php

namespace Drupal\findeter_user_form_request\Validator;

/**
 * Class ValidatorRegex.
 *
 * @package Drupal\findeter_user_form_request\Validator
 */
class ValidatorRegex extends BaseValidator {

  protected $pattern;

  /**
   * ValidatorRegex constructor.
   *
   * @param string $error_message
   *   Error message.
   * @param string $pattern
   *   Regex pattern.
   */
  public function __construct($error_message, $pattern) {
    parent::__construct($error_message);
    $this->pattern = $pattern;
  }

  /**
   * {@inheritdoc}
   */
  public function validates($value) {
    return preg_match($this->pattern, $value);
  }

}
