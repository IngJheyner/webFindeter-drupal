<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\node\Entity\Node;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreateTxt379Controller.
 */
class CreateTxt379 extends ControllerBase {

    /**
   * Crear formato 379 txt
   */
  public function reportFormat379(Request $request){

    $fecha_inicio = strtotime($request->query->get('trimestre-inicio'));
    $fecha_final = strtotime($request->query->get('trimestre-fin'));
    $fecha = strtotime(new DrupalDateTime());
    $fecha_periodo = date('dmY', $fecha_final);

    if(date('n',$fecha_inicio)>= 1 && date('n',$fecha_final) <= 3){
      $periodo = 1;
    }elseif(date('n',$fecha_inicio)>= 4 && date('n',$fecha_final) <= 6){
      $periodo = 2;
    }elseif(date('n',$fecha_inicio)>= 7 && date('n',$fecha_final) <= 9){
      $periodo = 3;
    }elseif(date('n',$fecha_inicio)>= 10 && date('n',$fecha_final) <= 12){
      $periodo = 4;
    }else
      $periodo = 'NA';


    $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
    $query->condition('status', 1)
          ->condition('type','pqrsd')
          ->condition('field_pqrsd_tipo_radicado', 'Reclamos', '=')
          ->condition('created', [$fecha_inicio, $fecha_final], 'BETWEEN');

    $result = $query->execute();


    if (!empty($result)) {
      foreach ($result as $nid_pqrsd) {
          $node = Node::load($nid_pqrsd);
          //dump(date('Y-m-d',$node->get('created')->getValue()[0]['value']));
      }
    }

    $my_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");

    if (!file_exists($my_path.'/reporte_379'))
      mkdir($my_path.'/reporte_379', 0755);

    $uri = $my_path.'/reporte_379/379_Trimestre_'.$periodo.'_'.date('Y').'.txt';

    $datos = "00001122000002".$fecha_periodo."00031CFINDETERCF0154\n000022000001100000\n0000343790103999+00000000000000000000\n0000443790104999+00000000000000000000\n0000543790110999+00000000000000000000\n000315";

    if (file_put_contents($uri, $datos) === FALSE)
      dump('El archivo no se pudo crear');

    $headers = [
      'Content-Type' => 'text/*',
      'Content-Description' => 'File Download',
      'Content-Disposition' => 'attachment; filename=formato379.txt'
    ];

    return new BinaryFileResponse($uri, 200, $headers, true );
  }

}