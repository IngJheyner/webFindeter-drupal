<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for findeter_pqrsd routes.
 */
class ActionComplaintsSmfcController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function consultComplaints() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
