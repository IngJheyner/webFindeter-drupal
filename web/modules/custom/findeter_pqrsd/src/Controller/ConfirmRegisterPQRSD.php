<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * Defines a route controller for watches autocomplete form elements.
 */
class ConfirmRegisterPQRSD extends ControllerBase {

  /**
   * Handler for autocomplete request.
   */
  public function confirmRegister($nid) {

    $nid = Xss::filter($nid);

    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $node = $storage->load($nid);

    $statusPQRSDPage = Url::fromRoute('findeter_pqrsd.status_pqrsd');
    $statusPQRSDLink = Link::fromTextAndUrl('aquí', $statusPQRSDPage);
    $statusPQRSDLink = $statusPQRSDLink->toRenderable();

    return [
      '#markup' => '<div class="container">
                    <div class="row my-5">
                    <div class="col-12">
                    <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading mb-2"><i class="fas fa-check-square"></i> Radicado registrado.</h4>
                    <p>Gracias por comunicarse con nosotros, su petición ha sido radicada satisfactoriamente.</p>
                    <p>Usted pódra consultar '.render($statusPQRSDLink).' el estado de su petición con el No. de radicado: <b>'.$node->get('field_pqrsd_numero_radicado')->getString().'</b> </p><hr>
                    <p>Su opinión es muy importante para nosotros. Lo invitamos a diligenciar la siguiente <a href="#">encuesta</a> de satisfacción para conitnuar mejorando nuestro servicio. </p>
                    </div></div></div></div>',
    ];

  }

}
