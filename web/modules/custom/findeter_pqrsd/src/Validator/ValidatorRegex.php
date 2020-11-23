<?php

namespace Drupal\findeter_pqrsd\Validator;

/**
 * Class ValidatorRegex.
 *
 * @package Drupal\findeter_pqrsd\Validator
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
