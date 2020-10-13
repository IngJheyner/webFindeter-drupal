<?php

namespace Drupal\findeter_user_form_request\Step;

/**
 * Class StepFinalize.
 *
 * @package Drupal\findeter_user_form_request\Step
 */
class StepFinalize extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_FINALIZE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($steps,$form,$form_state) {

    $formStep['completed'] = [
      '#markup' => '<h3>Gracias por comunicarse con nosotros, su Petición ha sido radicada satisfactoriamente.</h3> <p>(Texto temporal) Para consultar la lista de formularios: <a href="/admin/user-request-admin">click aquí</a></p>',
    ];
    		
    return $formStep;
  }

}
