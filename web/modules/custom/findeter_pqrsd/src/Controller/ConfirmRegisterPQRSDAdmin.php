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
class ConfirmRegisterPQRSDAdmin extends ControllerBase {

  /**
   * Handler for autocomplete request.
   */
  public function confirmRegister($operation, $nid) {

    $operation = Xss::filter($operation);
    $nid = Xss::filter($nid);

    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $node = $storage->load($nid);

    $wordOperation = '';
    switch($operation){
      case 'create':
        $wordOperation = '<b>Registró</b>';
        break;

      case 'update':
        $wordOperation = '<b>Actualizó</b>';
        break;

      default:
        $wordOperation = '<b>Aplicó la operación </b> a ';
        break;
    }

    $newPQRSD = Url::fromRoute('findeter_pqrsd.register_pqrsd_admin');
    $newPQRSDLink = Link::fromTextAndUrl('Crear una nueva radicatoria', $newPQRSD);
    $newPQRSDLink = $newPQRSDLink->toRenderable();

    $adminPage = Url::fromRoute('view.pqrsd.page_1');
    $adminPageLink = Link::fromTextAndUrl('Ir al panel de administración', $adminPage);
    $adminPageLink = $adminPageLink->toRenderable();

    return [
      '#markup' => '
        <div class="pqrsd-confirm">
          <div class="success">
            <p>Se '.$wordOperation.' la PQRSD satisfactoriamente</p>
            <div class="row">
              <span class="text">El número de radicado es:</span>
              <span class="value">'.$node->get('field_pqrsd_numero_radicado')->getValue()[0]['value'].'</span>
            </div>
            <div class="row">
              <span class="text">Asignado a:  </span>
              <span class="value">'.$node->get('uid')->entity->getAccountName().'</span>
            </div>
            <div class="row">
              <span class="text">Fecha de vencimiento:  </span>
              <span class="value">'.$node->get('field_pqrsd_fecha_roja')->getValue()[0]['value'].'</span>
            </div>
          </div>
          <div class="actions-success">Puede:
            <div class="link">'.render($newPQRSDLink).'</div>
            <div class="link">'.render($adminPageLink).'</div>
          </div>
        <div>'
    ];

  }

}
