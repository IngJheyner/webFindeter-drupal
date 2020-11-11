<?php

namespace Drupal\findeter_pqrsd\Button;

/**
 * Class BaseButton.
 *
 * @package Drupal\findeter_pqrsd\Button
 */
abstract class BaseButton implements ButtonInterface {

  /**
   * {@inheritdoc}
   */
  public function ajaxify() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubmitHandler() {
    return FALSE;
  }

}
