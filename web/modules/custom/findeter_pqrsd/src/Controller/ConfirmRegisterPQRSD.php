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
      '#markup' => '<p>Gracias por comunicarse con nosotros, su Petición ha sido radicada satisfactoriamente.</p>
                    <p>Usted pódra consultar '.render($statusPQRSDLink).' el estado de su petición con el No. de radicado: <b>'.$node->get('field_pqrsd_numero_radicado')->getString().'</b> </p>
                    <p>Su opinión es muy importante para nosotros. Lo invitamos a diligenciar <a href="#">aquí</a> la siguiente encuesta de satisfacción para conitnuar mejorando nuestro servicio. </p>',
    ];

  }

}
