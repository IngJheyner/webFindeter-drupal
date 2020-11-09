<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBuilder;
use \Symfony\Component\HttpFoundation\Response;

/**
 * Class AjaxCustomController.
 */
class UserRequestAjaxResponse extends ControllerBase {

  public function ajaxResponse(){
    $response = new AjaxResponse();

    $form = \Drupal::formBuilder()->getForm('Drupal\findeter_pqrsd\Form\UserRequestForm');

    $response->addCommand(new HtmlCommand('#form-wrapper',$form,[]));
    return $response;

  }


}
