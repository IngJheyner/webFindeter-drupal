<?php

namespace Drupal\findeter_pqrsd\Step;

/**
 * Class BaseStep.
 *
 * @package Drupal\findeter_pqrsd\Step
 */
abstract class BaseStep implements StepInterface {

  /**
   * Multi steps of the form.
   *
   * @var StepInterface
   */
  protected $step;

  /**
   * Values of element.
   *
   * @var array
   */
  protected $values;


  /**
   * {@inheritdoc}
   */
  public function getStep() {
    return $this->step;
  }

  /**
   * {@inheritdoc}
   */
  public function isLastStep() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setValues($values) {
    $this->values = $values;
  }

  /**
   * {@inheritdoc}
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  abstract protected function setStep();
}
