<?php

namespace Drupal\findeter_pqrsd\Step;

/**
 * Class StepFinalize.
 *
 * @package Drupal\findeter_pqrsd\Step
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

    // retry the last value, the nid of user request
    $valueNid = $steps[4]->getValues();

    $formStep['completed'] = [
      '#markup' => '<p>Gracias por comunicarse con nosotros, su Petición ha sido radicada satisfactoriamente.</p>
                    <p>Usted pódra consultar <a href="#">aquí</a> el estado de su petición con el No. de radicado: <b>'.$valueNid['pqrsd_numero_radicado'].'</b> </p>
                    <p>Su opinión es muy importante para nosotros. Lo invitamos a diligenciar <a href="#">aquí</a> la siguiente encuesta de satisfacción para conitnuar mejorando nuestro servicio. </p>',
    ];
    		
    return $formStep;
  }

}
