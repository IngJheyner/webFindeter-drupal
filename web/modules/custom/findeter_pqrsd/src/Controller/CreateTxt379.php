<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

    /* ===== ===== Que la fecha de inicio y final no esten vacias ===== ===== */
    if(!empty($fecha_inicio) && !empty($fecha_final)){

      //$fecha = strtotime(new DrupalDateTime());
      $fecha_periodo = date('dmY', $fecha_final);

      /* ===== ===== Se valida una fecha trimestral ===== ===== */
      if((date('n',$fecha_inicio)>= 1 && date('n',$fecha_final) <= 3) && (date('n',$fecha_inicio) !== date('n',$fecha_final)) && (date('Y',$fecha_inicio) === date('Y',$fecha_final))){
        $periodo = 1;
      }elseif((date('n',$fecha_inicio)>= 4 && date('n',$fecha_final) <= 6) && (date('n',$fecha_inicio) !== date('n',$fecha_final)) && (date('Y',$fecha_inicio) === date('Y',$fecha_final))){
        $periodo = 2;
      }elseif((date('n',$fecha_inicio)>= 7 && date('n',$fecha_final) <= 9) && (date('n',$fecha_inicio) !== date('n',$fecha_final)) && (date('Y',$fecha_inicio) === date('Y',$fecha_final))){
        $periodo = 3;
      }elseif((date('n',$fecha_inicio)>= 10 && date('n',$fecha_final) <= 12) && (date('n',$fecha_inicio) !== date('n',$fecha_final)) && (date('Y',$fecha_inicio) === date('Y',$fecha_final))){
        $periodo = 4;
      }else{
        $this->messenger()->addWarning('La fecha seleccionada no corresponde a un periodo trimestral.');
        $url = Url::fromRoute('view.pqrsd.page_2');
        $response = new RedirectResponse($url->toString(), 302);
        return $response->send();
      }

      /* ===== ===== CONSULTA DE LOS RADICADOS PENDIENTES POR RESPONDER DE LOS TRIMESTRES PASADOS ===== ===== */
      $query_all = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
      $query_all->condition('status', 1)
            ->condition('type','pqrsd')
            ->condition('field_pqrsd_tipo_radicado', 'Reclamos', '=')
            ->condition('created', [strtotime('01-01-2020'), $fecha_final], 'BETWEEN');
      $resultAll = $query_all->execute();

      $re_pe_1 = 0;//1
      $re_pe_2 = 0;//1
      $re_pe_3 = 0;//1

      $re_rb_1 = 0;//2
      $re_rb_2 = 0;//2
      $re_rb_3 = 0;//2

      $datos = "";

      /* ===== ===== Inicio del archivo plano ===== ===== */
      $datos .= "00001122000002".$fecha_periodo."00031CFINDETERCF0154\n000022000001100000\n";

      foreach ($resultAll as $nid_pqrsd) {

        $node = Node::load($nid_pqrsd);
        $productos = $node->get('field_pqrsd_nombre_producto')->getValue()[0]['value'];

        switch ($productos) {

          case 'credito':

            if(!isset($node->get('field_pqrsd_respuesta')->getValue()[0]['value']))
              $re_pe_1 += 1;//1
            if($node->get('created')->getValue()[0]['value'] >= $fecha_inicio)
              $re_rb_1 += 1;//2
            break;

          case 'microcredito':

            if(!isset($node->get('field_pqrsd_respuesta')->getValue()[0]['value']))
              $re_pe_2 += 1;//1
            if($node->get('created')->getValue()[0]['value'] >= $fecha_inicio)
              $re_rb_2 += 1;//2
            break;

          case 'capacitacion':

            if($node->get('created')->getValue()[0]['value'] < $fecha_inicio){

              if(isset($node->get('field_pqrsd_respuesta')->getValue()[0]['value'])){
                if($node->get('field_pqrsd_fecha_respuesta')->getValue()[0]['value'] >= date("Y-m-d", $fecha_inicio)){
                  $re_pe_3 += 1;//1
                }
              }else{
                $re_pe_3 += 1;//1
              }
            }

            if($node->get('created')->getValue()[0]['value'] >= $fecha_inicio)
              $re_rb_3 += 1;//2
            break;
        }

      }
      /* ===== ===== Columna 1 ===== ===== */
      $total_re_pe = $re_pe_1 + $re_pe_2 + $re_pe_3;//1
      $datos .= "0000343790103999+".str_pad($re_pe_1, 20, '0', STR_PAD_LEFT)."\n0000443790104999+".str_pad($re_pe_2, 20, '0', STR_PAD_LEFT)."\n0000543790110999+".str_pad($re_pe_3, 20, '0', STR_PAD_LEFT)."\n0000643790163999+".str_pad($total_re_pe, 20, '0', STR_PAD_LEFT)."\n";

      /* ===== ===== Columna 2 ===== ===== */
      $total_re_rb = $re_rb_1 + $re_rb_2 + $re_rb_3;//2
      $datos .= "0000743790203999+".str_pad($re_rb_1, 20, '0', STR_PAD_LEFT)."\n0000843790204999+".str_pad($re_rb_2, 20, '0', STR_PAD_LEFT)."\n0000943790210999+".str_pad($re_rb_3, 20, '0', STR_PAD_LEFT)."\n0001043790263999+".str_pad($total_re_rb, 20, '0', STR_PAD_LEFT)."\n";

      /* ===== ===== Columna 3 ===== ===== */
      $subtotal_pe_rb_1 = $re_pe_1 + $re_rb_1;//3
      $subtotal_pe_rb_2 = $re_pe_2 + $re_rb_2;//3
      $subtotal_pe_rb_3 = $re_pe_3 + $re_rb_3;//3
      $total_pe_rb = $total_re_pe + $total_re_rb;//3
      $datos .= "0001143790303999+".str_pad($subtotal_pe_rb_1, 20, '0', STR_PAD_LEFT)."\n0001243790304999+".str_pad($subtotal_pe_rb_2, 20, '0', STR_PAD_LEFT)."\n0001343790310999+".str_pad($subtotal_pe_rb_3, 20, '0', STR_PAD_LEFT)."\n0001443790363999+".str_pad($total_pe_rb, 20, '0', STR_PAD_LEFT)."\n";

      /* ===== =====  ===== ===================== */
      /* CONSULTA DE LOS REGISTROS DEL TRIMESTRE */
      /* ===== =====  ===== ===================== */
      $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
      $query->condition('status', 1)
            ->condition('type','pqrsd')
            ->condition('field_pqrsd_tipo_radicado', 'Reclamos', '=')
            ->condition('field_pqrsd_fecha_respuesta', [date("Y-m-d",$fecha_inicio), date("Y-m-d",$fecha_final)], 'BETWEEN');
      $result = $query->execute();

      $re_fin_1=0;//4
      $re_fin_2=0;//4
      $re_fin_3=0;//4

      $re_con_1=0;//5
      $re_con_2=0;//5
      $re_con_3=0;//5

      foreach ($result as $nid_pqrsd) {

        $node = Node::load($nid_pqrsd);
        $productos = $node->get('field_pqrsd_nombre_producto')->getValue()[0]['value'];

        switch ($productos) {

          case 'credito':

            if(isset($node->get('field_pqrsd_respuesta')->getValue()[0]['value']) &&  isset($node->get('field_pqrsd_respuesta_a_favor')->getValue()[0]['value'])){

              if($node->get('field_pqrsd_respuesta_a_favor')->getValue()[0]['value'] == 'findeter'){
                $re_fin_1 += 1;//4
              }else{
                $re_con_1 += 1;//5
              }

            }
          break;

          case 'microcredito':

            if(isset($node->get('field_pqrsd_fecha_respuesta')->getValue()[0]['value']) &&  isset($node->get('field_pqrsd_respuesta_a_favor')->getValue()[0]['value'])){
              if($node->get('field_pqrsd_respuesta_a_favor')->getValue()[0]['value'] == 'findeter'){
                $re_fin_2 += 1;//4
              }else{
                $re_con_2 += 1;//5
              }
            }
          break;

          case 'capacitacion':

            if(isset($node->get('field_pqrsd_respuesta')->getValue()[0]['value']) && isset($node->get('field_pqrsd_respuesta_a_favor')->getValue()[0]['value'])){

              if($node->get('field_pqrsd_respuesta_a_favor')->getValue()[0]['value'] == 'findeter'){
                $re_fin_3 += 1;//4
              }else{
                $re_con_3 += 1;//5
              }
            }
          break;
        }

      }
      /* ===== ===== Columna 4 ===== ===== */
      $total_re_con = $re_con_1 + $re_con_2 + $re_con_3;
      $datos .= "0001543790403999+".str_pad($re_con_1, 20, '0', STR_PAD_LEFT)."\n0001643790404999+".str_pad($re_con_2, 20, '0', STR_PAD_LEFT)."\n0001743790410999+".str_pad($re_con_3, 20, '0', STR_PAD_LEFT)."\n0001843790463999+".str_pad($total_re_con, 20, '0', STR_PAD_LEFT)."\n";

      /* ===== ===== Columna 5 ===== ===== */
      $total_re_fin = $re_fin_1 + $re_fin_2 + $re_fin_3;
      $datos .= "0001943790503999+".str_pad($re_fin_1, 20, '0', STR_PAD_LEFT)."\n0002043790504999+".str_pad($re_fin_2, 20, '0', STR_PAD_LEFT)."\n0002143790510999+".str_pad($re_fin_3, 20, '0', STR_PAD_LEFT)."\n0002243790563999+".str_pad($total_re_fin, 20, '0', STR_PAD_LEFT)."\n";

      /* ===== ===== Columna 6 ===== ===== */
      $subtotal_fin_con_1 = $re_con_1 + $re_fin_1;
      $subtotal_fin_con_2 = $re_con_2 + $re_fin_2;
      $subtotal_fin_con_3 = $re_con_3 + $re_fin_3;
      $total_fin_con = $total_re_con + $total_re_fin;
      $datos .= "0002343790603999+".str_pad($subtotal_fin_con_1, 20, '0', STR_PAD_LEFT)."\n0002443790604999+".str_pad($subtotal_fin_con_2, 20, '0', STR_PAD_LEFT)."\n0002543790610999+".str_pad($subtotal_fin_con_3, 20, '0', STR_PAD_LEFT)."\n0002643790663999+".str_pad($total_fin_con, 20, '0', STR_PAD_LEFT)."\n";

      /* ===== ===== Columna 7 ===== ===== */
      $re_tra_1 = $subtotal_pe_rb_1 - $subtotal_fin_con_1;
      $re_tra_2 = $subtotal_pe_rb_2 - $subtotal_fin_con_2;
      $re_tra_3 = $subtotal_pe_rb_3 - $subtotal_fin_con_3;
      $total_tra = $re_tra_1 + $re_tra_2 + $re_tra_3;
      //$total_tra = $re_tra_1 + $re_tra_2 + $re_tra_3;
      $datos .= "0002743790703999+".str_pad($re_tra_1, 20, '0', STR_PAD_LEFT)."\n0002843790704999+".str_pad($re_tra_2, 20, '0', STR_PAD_LEFT)."\n0002943790710999+".str_pad($re_tra_3, 20, '0', STR_PAD_LEFT)."\n0003043790763999+".str_pad($total_tra, 20, '0', STR_PAD_LEFT)."\n";

      $datos .= "000315";

    }else{

      $this->messenger()->addWarning('Seleccione una fecha de  creación trimestral para el fomato 379.');
      $url = Url::fromRoute('view.pqrsd.page_2');
      $response = new RedirectResponse($url->toString(), 302);
      return $response->send();

    }

    $my_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");

    if (!file_exists($my_path.'/reporte_379'))
      mkdir($my_path.'/reporte_379', 0755);

    $file_name = "379_Trimestre_".$periodo."_".date('Y').".txt";
    $uri = $my_path."/reporte_379/$file_name";

    if (file_put_contents($uri, $datos) === FALSE){
      $this->messenger()->addError('Hubo problemas al crear el archivo. Si el problema persiste ponerse en contacto con el administrador. Linea 227');
      $url = Url::fromRoute('view.pqrsd.page_2');
      $response = new RedirectResponse($url->toString(), 302);
      return $response->send();
    }

    $headers = [
      'Content-Type' => 'text/*',
      'Content-Description' => 'File Download',
      'Content-Disposition' => "attachment; filename=$file_name"
    ];

    return new BinaryFileResponse($uri, 200, $headers, true );

  }

}