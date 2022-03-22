<?php

namespace Drupal\findeter_pqrsd\Step;

use Drupal\Core\Link;
use Drupal\Core\Url;

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
    $valueNid = $steps[3]->getValues();
    //'.$valueNid['pqrsd_numero_radicado'].'</b>

    $statusPQRSDPage = Url::fromRoute('findeter_pqrsd.status_pqrsd');
    $statusPQRSDLink = Link::fromTextAndUrl('aquí', $statusPQRSDPage);
    $statusPQRSDLink = $statusPQRSDLink->toRenderable();

    $pagePathRegister = Url::fromRoute('findeter_pqrsd.register_pqrsd');
    $opcionesPathRegister = [
      'attributes' => [
        'class' => [
          'btn',
          'btn-primary',
          'text-white mt-3'
        ],
      ],
    ];
    $pagePathRegister->setOptions($opcionesPathRegister);
    $pageRegister = Link::fromTextAndUrl('Registrar PQRSD', $pagePathRegister);
    $pageRegister = $pageRegister->toRenderable();

    $num_settled = isset($valueNid['pqrsd_numero_radicado']) ? $valueNid['pqrsd_numero_radicado'] : '';      

    $formStep['completed'] = [
      '#markup' => '<div class="container">
                    <div class="row my-5">
                    <div class="col-12">
                    <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading mb-2"><i class="fas fa-check-square"></i> Radicado registrado.</h4>
                    <p>Gracias por comunicarse con nosotros, su petición ha sido radicada satisfactoriamente.</p>
                    <p>Usted pódra consultar '.render($statusPQRSDLink).' el estado de su petición con el No. de radicado: <span class="font-weight-bold text-dark">'.$num_settled.'</span></p><hr>
                    <p>Su opinión es muy importante para nosotros. Lo invitamos a diligenciar la siguiente <a href="#">encuesta</a> de satisfacción para conitnuar mejorando nuestro servicio. </p>
                    '.render($pageRegister).'
                    </div>
                    </div>
                    </div>
                    </div>',
    ];

    return $formStep;
  }

}
