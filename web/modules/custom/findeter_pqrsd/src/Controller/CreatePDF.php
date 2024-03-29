<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

use Drupal\findeter_pqrsd\Wkhtmlto\Pdf;
use Drupal\views\Views;

/**
 * Class CreatePDFController.
 */
class CreatePDF extends ControllerBase {

  /**
   * Create a pdf file from HTML of node.
   */
  public function nodeToPdf($nid) {

    $config = $this->config('findeter_pqrsd.admin');

    $entity_type = 'node';
    $view_mode = $config->get('view_mode');
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
    $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
    $node = $storage->load($nid);
    $build = $view_builder->view($node, $view_mode);
    $output = render($build);

    $css_path = $config->get('css_path');
    if ($css_path == '') {
      $module_handler = \Drupal::service('module_handler');
      $module_path = $module_handler->getModule('findeter_pqrsd')->getPath();
      $css_path = $module_path.'/css/pdf.css';
    }

    $options = array(
      'encoding' => 'UTF-8',
      'no-outline',

      // Default page options.
      'disable-smart-shrinking',
      'user-style-sheet' => $css_path,
    );

    // You can pass a filename, a HTML string, an URL or an options array to the constructor
    $pdf = new Pdf($output->__toString());
    $pdf->setOptions($options);

    // On some systems you may have to set the path to the wkhtmltopdf executable
    // $pdf->binary = 'C:\...';
    $default_schema = \Drupal::config('system.file')->get('default_scheme');
    $my_path = \Drupal::service('file_system')->realpath($default_schema . "://");
    //$my_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");

    if (!$pdf->saveAs($my_path.'/request_'.$nid.'.pdf')) {
        $error = $pdf->getError();
    }

    /*//Show file in browser
    if (!$pdf->send()) {
      $error = $pdf->getError();
      // ... handle error here
    }*/

    // ... or send to client as file download
    if (!$pdf->send('request_'.$nid.'.pdf')) {
      $error = $pdf->getError();
    }

    return [
      '#type'   => 'markup',
      '#markup' => $this->t('Implement method: hello with parameter(s): $nid'),
    ];

  }


  /**
   * Create a pdf file from html of view rendered with some arguments.
   */
  public function reportToPdf($filter) {

    $view = Views::getView('pqrsd');
    $view->setDisplay('page_2');

    if ($filter != 'filter') {
      // Set Exposed filter argument
      parse_str($filter, $filtersArray);
      $view->setExposedInput($filtersArray);
    }

    $title = $view->getTitle();
    $render = $view->render();
    //$output = drupal_render($render);
    $output = \Drupal::service('renderer')->render($render);

    $config = $this->config('findeter_pqrsd.admin');

    $css_path = $config->get('css_path');
    if($css_path == ''){
      $module_handler = \Drupal::service('module_handler');
      $module_path = $module_handler->getModule('findeter_pqrsd')->getPath();
      $css_path = $module_path.'/css/pdf.css';
    }
    $options = array(
      'encoding' => 'UTF-8',  // option with argument
      'no-outline',         // Make Chrome not complain
      'margin-top'    => 0,
      'margin-right'  => 0,
      'margin-bottom' => 0,
      'margin-left'   => 0,
      'page-width'    => 2800,
      'orientation' => 'landscape',

      // Default page options.
      'disable-smart-shrinking',
      'user-style-sheet' => $css_path,
    );

    // You can pass a filename, a HTML string, an URL or an options array to the constructor.
    $pdf = new Pdf('<h3 class="report-title">Reporte de datos PQRSD</h3><div class="report-generated">Generado el: '.date('d/m/Y').'</div>'.$output->__toString());
    $pdf->setOptions($options);

    // On some systems you may have to set the path to the wkhtmltopdf executable.
    // $pdf->binary = 'C:\...';
    $default_schema = \Drupal::config('system.file')->get('default_scheme');
    $my_path = \Drupal::service('file_system')->realpath($default_schema . "://");
    //$my_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");

    if (!$pdf->saveAs($my_path.'/report-'.date('d/m/Y-H:i:s').'.pdf')) {
        $error = $pdf->getError();
    }

    /*//Show file in browser
    if (!$pdf->send()) {
      $error = $pdf->getError();
      // ... handle error here
    }*/

    // ... or send to client as file download
    if (!$pdf->send('report-'.date('d/m/Y-H:i:s').'.pdf')) {
      $error = $pdf->getError();
    }

    return [
      '#type'   => 'markup',
      '#markup' => $this->t('Implement method: hello with parameter(s): $nid'),
    ];

  }

}
