<?php

namespace Drupal\findeter_user_form_request\Button;

/**
 * Class BaseButton.
 *
 * @package Drupal\findeter_user_form_request\Button
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
