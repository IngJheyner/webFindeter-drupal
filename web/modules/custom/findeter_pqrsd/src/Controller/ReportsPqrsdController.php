<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

/**
 * Returns responses for Findeter PQRSD Manager routes.
 * @author 3ddesarrollo <email@email.com>
 * @param EntityFieldManagerInterface $field_manager
 */
class ReportsPqrsdController extends ControllerBase {

  /**
   * @var EntityFieldManagerInterface $fieldManager;
   */
  protected $fieldManager;

  /**
   * @var EntityTypeManager $entityTypeManager;
   */
  protected $entityTypeManager;

  public function __construct(EntityFieldManagerInterface $field_manager, EntityTypeManager $entity_type_manager){

    $this->fieldManager = $field_manager;
    $this->entityTypeManager = $entity_type_manager;

  }

  public static function create(ContainerInterface $container)
  {
    return new static(
     $container->get('entity_field.manager'),
     $container->get('entity_type.manager')
    );
  }

  /**
   * Builds the response.
   */
  public function statistics() {

    $spanishMonths['January'] = 'Enero';
    $spanishMonths['February'] = 'Febrero';
    $spanishMonths['March'] = 'Marzo';
    $spanishMonths['April'] = 'Abril';
    $spanishMonths['May'] = 'Mayo';
    $spanishMonths['June'] = 'Junio';
    $spanishMonths['July'] = 'Julio';
    $spanishMonths['August'] = 'Agosto';
    $spanishMonths['September'] = 'Septiembre';
    $spanishMonths['October'] = 'Octubre';
    $spanishMonths['November'] = 'Noviembre';
    $spanishMonths['December'] = 'Diciembre';

    //Settings para los siguientes campos y ser tratados para mostrar.
    $definitions = $this->fieldManager->getFieldDefinitions('node', 'pqrsd');
    
    //Tipo de solicitante
    $settingsTipSoli = $definitions['field_pqrsd_tipo_solicitante']->getSettings()['allowed_values'];
    $tipoSolicitanteCount = [];

    foreach ($settingsTipSoli as $key => $value) {
      $tipoSolicitanteCount[$value] = 0;
    }

    //Nombre de producto
    $settingsNameProduct = $definitions['field_pqrsd_nombre_producto']->getSettings()['allowed_values'];
    $tipoNameProductCount = [];

    foreach ($settingsNameProduct as $key => $value) {
      $tipoNameProductCount[$value] = 0;
    }

    //Forma de recepcion
    $settingsFormRecep = $definitions['field_pqrsd_forma_recepcion']->getSettings()['allowed_values'];
    $FormRecepCount = [];

    foreach ($settingsFormRecep as $key => $value) {
      $FormRecepCount[$value] = 0;
    }

    //Inicializamos el arreglo
    $data = [];

    //Realizamos una consulta para traer los nids de todo los nodos que perteneces al tipo de contenido pqrsd
    $query = \Drupal::entityQuery('node')
                ->condition('type', 'pqrsd')
                ->execute();

    $nodeStorage = $this->entityTypeManager->getStorage('node');

    foreach ($query as $nid){

      //Cargamos cada uno de los nodos con su correspondiete datos o valor
      $node = $nodeStorage->load($nid);

      /* ===== ===== Definimos valores para los graficos charts ===== ===== */

      //Registro mensula de radicados
      $month = date('F',$node->get('created')->value);
      $month = $spanishMonths[$month];

      if(!isset($data['monthly'][$month])){
        $data['monthly'][$month] = 0;
      }
      $data['monthly'][$month]++;

      //Tipo de radicado
      $tipoRadicado = $node->get('field_pqrsd_tipo_radicado')->value;

      if(!isset($data['tipoRadicado'][$tipoRadicado])){
        $data['tipoRadicado'][$tipoRadicado] = 0;
      }
      $data['tipoRadicado'][$tipoRadicado]++;

      //Tipo de solicitante
      $tipoSolicitante = $node->get('field_pqrsd_tipo_solicitante')->value;
      
      foreach ($settingsTipSoli as $key => $value) {
        if($tipoSolicitante == $key){
          $tipoSolicitanteCount[$value]++;        
          break;
        }
      }     

      //Tipo de radicado
      $tipoProducto = $node->get('field_pqrsd_nombre_producto')->value;

      foreach ($settingsNameProduct as $key => $value) {
        if($tipoProducto == $key){
          $tipoNameProductCount[$value]++;          
          break;
        }
      }      

      // define data for "Canal recepción" field
      $canalRecepcion = $node->get('field_pqrsd_canal_recepcion')->value;
      if(!isset($data['canalRecepcion'][$canalRecepcion])){
        $data['canalRecepcion'][$canalRecepcion] = 0;
      }
      $data['canalRecepcion'][$canalRecepcion]++;

      // define data for "Forma recepción" field
      $formaRecepcion = $node->get('field_pqrsd_forma_recepcion')->value;

      foreach ($settingsFormRecep as $key => $value) {
        if($formaRecepcion == $key){
          $FormRecepCount[$value]++;          
          break;
        }
      }

    }

    //Tipos de solictante valores
    $tipoSolicitanteValue = array_filter(
      $tipoSolicitanteCount,
      function($item){ return $item > 0; });
    
    $data['tipoSolicitante'] = $tipoSolicitanteValue;
    
    //Nombre de productos
    $tipoNameProductValue = array_filter(
      $tipoNameProductCount,
      function($item){ return $item > 0; });
    
    $data['tipoProducto'] = $tipoNameProductValue;

    //Forma de recepcion
    $FormRecepValue = array_filter(
      $FormRecepCount,
      function($item){ return $item > 0; });
    
    $data['formaRecepcion'] = $FormRecepValue;

    $build['charts-graph'] = [
      '#type' => 'item',
      '#markup' => Markup::create('
        <div class="list-charts">
          <div class="chart-container"><canvas id="time-chart"></canvas></div>
          <div class="chart-container"><canvas id="tipo-radicado-chart"></canvas></div>
          <div class="chart-container"><canvas id="tipo-solicitante-chart"></canvas></div>
          <div class="chart-container"><canvas id="tipo-producto-chart"></canvas></div>
          <div class="chart-container"><canvas id="canal-recepcion-chart"></canvas></div>
          <div class="chart-container"><canvas id="forma-recepcion-chart"></canvas></div>
        '),
      '#attached' => [
        'drupalSettings' => [
          'pqrsdReports' => $data,
        ],
        'library' => [
          'findeter_pqrsd/admin_pages',
        ]
      ],
    ];

    return $build;

  }

  /**
   * Builds the response.
   */
  public function resultsDataTable() {

    //Storage de tipo entidad node
    $nodeStorage = $this->entityTypeManager->getStorage('node');

    //Agregamos u arreglo con los tipos de PQRSD
    $tipoRadicados = ['Peticiones','Quejas','Reclamos','Sugerencias','Denuncias','Total'];

    //Inicializamos valores en 0
    foreach($tipoRadicados as $tr){
      $mResume[$tr]['name'] = $tr;
      $mResume[$tr]['recibidas'] = 0;
      $mResume[$tr]['por_resolver'] = 0;
      $mResume[$tr]['favor_consumidor'] = 0;
      $mResume[$tr]['favor_entidad'] = 0;
      $mResume[$tr]['cerrados'] = 0;
      $mResume[$tr]['cerrado_a_tiempo'] = 0;
      $mResume[$tr]['vencidas'] = 0;
      $mResume[$tr]['info_publica'] = 0;
      $mResume[$tr]['trasladadas'] = 0;
    }

    $data = []; //Creamos una variable para almacenar la data

    //Realizamos una consulta para traer los nid de pqrsd y ser iterado por cada uno de ellos
    $query = \Drupal::entityQuery('node')
                ->condition('type', 'pqrsd')
                ->execute();

    foreach ($query as $nid) {

      $node = $nodeStorage->load($nid);//Cargamos lo nid de nodos pqrsd

      //define data for "Tipo radicado" field.
      $tipoRadicado = $node->get('field_pqrsd_tipo_radicado')->value;

      if(!isset($data['tipoRadicado'][$tipoRadicado])){
        $data['tipoRadicado'][$tipoRadicado] = 0;
      }
      $data['tipoRadicado'][$tipoRadicado]++;

      /* ===== ===== TABLA DE RESULTADOS ===== ===== */
      $now = strtotime('today');
      $fOrange = strtotime($node->get('field_pqrsd_fecha_naranja')->value);
      $fRed = strtotime($node->get('field_pqrsd_fecha_roja')->value);
      $fAnswer = strtotime($node->get('field_pqrsd_fecha_respuesta')->value);

      // PQRSD cerradas/abiertas
      $mResume[$tipoRadicado]['name'] = $tipoRadicado;
      $mResume[$tipoRadicado]['recibidas'] = $data['tipoRadicado'][$tipoRadicado];

      if($node->get('field_pqrsd_respuesta')->value == ''){

        $mResume[$tipoRadicado]['por_resolver']++;

        if($now > $fRed){
          $mResume[$tipoRadicado]['vencidas']++;
        }
      }else{

        $mResume[$tipoRadicado]['cerrados']++;

        if($node->get('field_pqrsd_respuesta_a_favor')->value == 'findeter'){
          $mResume[$tipoRadicado]['favor_entidad']++;
        }

        if($node->get('field_pqrsd_respuesta_a_favor')->value == 'consumidor'){
          $mResume[$tipoRadicado]['favor_consumidor']++;
        }

        if($fAnswer < $fRed){
          $mResume[$tipoRadicado]['cerrado_a_tiempo']++;
        }

        if( strpos($node->get('field_pqrsd_respuesta')->value, 'PQRSD Trasladada a:') ){
          $mResume[$tipoRadicado]['trasladadas']++;
        }

      }

      if($node->get('field_pqrsd_tipo_peticion')->value == 'publica') {
        $mResume[$tipoRadicado]['info_publica']++;
      }

    }

    foreach ($mResume as $tR=>$values){
      if($tR != 'Total'){
        $mResume['Total']['recibidas'] += $values['recibidas'];
        $mResume['Total']['por_resolver'] += $values['por_resolver'];
        $mResume['Total']['favor_consumidor'] += $values['favor_consumidor'];
        $mResume['Total']['favor_entidad'] += $values['favor_entidad'];
        $mResume['Total']['cerrados'] += $values['cerrados'];
        $mResume['Total']['cerrado_a_tiempo'] += $values['cerrado_a_tiempo'];
        $mResume['Total']['vencidas'] += $values['vencidas'];
        $mResume['Total']['info_publica'] += $values['info_publica'];
        $mResume['Total']['trasladadas'] += $values['trasladadas'];
      }
    }

    //Reconstruimos de nuevo la tabla para ser renderizada con la data
    $buildTable = [
      'container' => [
        '#type' => 'container',
        '#attributes' => [
          'id'=> "results-table-reports",
        ],         
        'table' => [
          '#theme' => 'table',
          '#rows' => $mResume,
          '#weight' => 1,
          '#prefix' => '<br><hr><h3>Tabla resumen de resultados</h3>',
          '#header' => [
            'Tipo',
            'Recibidas',
            'Pendientes por resolver',
            'Aprobadas a favor del consumidor financiero',
            'Aprobadas a favor de la entidad',
            'Trámites concluidos (cerrados)',
            'Resultas a tiempo',
            'Vencidas',
            'Solicitud acceso información pública',
            'Solicitudes trasladas a otra institución'
          ],
        ],
      ],
    ];

    //Creamos un evento ajax aync para mostrar la data, se remplace la tabla con los nuevos valores
    $response = new AjaxResponse();

    $response->addCommand(new ReplaceCommand(
      '#results-table-reports',
      \Drupal::service('renderer')->render($buildTable),
    ));

    return $response;

  }  

}
