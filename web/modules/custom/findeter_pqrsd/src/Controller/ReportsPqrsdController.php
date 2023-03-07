<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Cmixin\BusinessDay;
use Carbon\Carbon;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\findeter_pqrsd\Api\ApiSmfcInterface;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Returns responses for Findeter PQRSD Manager routes.
 *
 * @author 3ddesarrollo <email@email.com>
 */
class ReportsPqrsdController extends ControllerBase {

  /**
   * FieldManager.
   *
   * @var Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldManager;

  /**
   * EntityTypeManager.
   *
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Render.
   *
   * @var Drupal\Core\Render\RendererInterface
   */
  protected $render;

  /**
   * CacheInterface.
   *
   * @var Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * LanguageInterface.
   *
   * @var Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * FormBuilder.
   *
   * @var Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * State.
   *
   * @var Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * API SMFC.
   *
   * @var Drupal\findeter_pqrsd\Api\ApiSmfcInterface
   */
  protected $apiSmfc;

  /**
   * Messenger.
   *
   * @var Drupal\findeter_pqrsd\Api\ApiSmfcInterface
   */
  protected $messenge;

  /**
   * Servicios.
   *
   * @param Drupal\Core\Entity\EntityFieldManagerInterface $field_manager
   *   FieldManager.
   * @param Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   EntityTypeManager.
   * @param Drupal\Core\Render\RendererInterface $render
   *   Render.
   * @param Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   LanguageManager.
   * @param Drupal\Core\Cache\CacheBackendInterface $cache
   *   Cache.
   * @param Drupal\Core\Form\FormBuilderInterface $form_builder
   *   Form Builder.
   * @param Drupal\Core\State\StateInterface $state
   *   State.
   * @param Drupal\findeter_pqrsd\Api\ApiSmfcInterface $api_smfc
   *   API SMFC.
   * @param Drupal\Core\Messenger\MessengerInterface $messenger
   *   Messenger.
   */
  public function __construct(EntityFieldManagerInterface $field_manager, EntityTypeManager $entity_type_manager, RendererInterface $render, LanguageManagerInterface $language_manager, CacheBackendInterface $cache, FormBuilderInterface $form_builder, StateInterface $state, ApiSmfcInterface $api_smfc, MessengerInterface $messenger) {

    $this->fieldManager = $field_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->render = $render;
    $this->languageManager = $language_manager;
    $this->cache = $cache;
    $this->formBuilder = $form_builder;
    $this->state = $state;
    $this->apiSmfc = $api_smfc;
    $this->messenge = $messenger;

  }

  /**
   * Inyeccion dependencias.
   *
   * @param Symfony\Component\DependencyInjection\ContainerInterface $container
   *   Mixed.
   *
   * @return mixed
   *   Return services.
   */
  public static function create(ContainerInterface $container) {
    return new static(
     $container->get('entity_field.manager'),
     $container->get('entity_type.manager'),
     $container->get('renderer'),
     $container->get('language_manager'),
     $container->get('cache.default'),
     $container->get('form_builder'),
     $container->get('state'),
     $container->get('api.smfc'),
    $container->get('messenger')
    );
  }

  /**
   * Estadistica de PQRSD en graficos.
   *
   * @return mixed
   *   Return render html.
   */
  public function statistics(Request $request) {
    // dump($request->request->get('message'));
    // Creamos variables de inicializacion en tiempo y valores como el cid de cache creado en cache.default.
    $startTime = microtime(TRUE);
    $cid = "findeter_pqrsd_statistics: {$this->languageManager->getCurrentLanguage()->getId()}";
    $data = NULL;
    $fromCache = FALSE;

    // Validacion de cache, sino ejecutamos la funcion para generar una nueva consulta.
    if (($cache = $this->cache->get($cid)) && is_null($request->request->get('start_date'))) {
      $data = $cache->data;
      $fromCache = TRUE;
    }
    else {
      $data = $this->statisticsData($request);
      if (is_null($request->request->get('start_date'))) {
        $this->cache->set($cid, $data, Cache::PERMANENT, ['node_list:pqrsd']);
      }
    }

    $endTime = microtime(TRUE);
    $duration = $endTime - $startTime;

    if (empty($data)) {

      $messageString = [
        '#markup' => "<h3>{$this->t('No results found')}</h3>",
      ];

    }
    else {

      $messageString[] = [
        '#markup' => $this->t("<h3>Info. Cached:</h3>"),
      ];

      $messageString[] = [
        '#markup' => $this->t('Execution time: @time ms',
        [
          '@time' => number_format($duration * 1000, 2)
        ])
      ];

      $messageString[] = [
        '#markup' => $this->t('<br>Source: @source', ['@source' => !$fromCache ? $this->t('Database query') : $this->t('Cache')])
      ];

      if ($fromCache) {

        $cacheTimeStamp = \Drupal::service('date.formatter')->format($cache->created, 'short');

        $messageString[] = [
          '#markup' => $this->t('<br>Cache time @cacheTime', ['@cacheTime' => $cacheTimeStamp]),
        ];

      }

      // Formulario de filtro para estadisticas.
      $form = $this->formBuilder->getForm('Drupal\findeter_pqrsd\Form\ReportsPqrsdForm');

      $build[]['form-filter'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $this->render->render($form),
        '#weight' => 0,
      ];

      // Definir peridod de fechas consultas.
      if (is_null($request->request->get('start_date'))) {

        $start_date = date("d-m-Y", strtotime('first day of January', time()));
        $finished_date = date("d-m-Y", \Drupal::time()->getCurrentTime());

      }
      else {
        $start_date = date("d-m-Y", strtotime($request->request->get('start_date')['date']));
        $finished_date = date("d-m-Y", strtotime($request->request->get('finished_date')['date']));
      }

      $build[]['charts-graph'] = [
        '#type' => 'item',
        '#markup' => Markup::create('
          <time id="time-period"> Periodo '.$start_date.' al '.$finished_date.'</time>
          <div class="list-charts">
            <div class="chart-container">
              <canvas id="time-chart"></canvas>
              <table id="time-chart"><thead><tr><th>Mes</th><th>Total</th></tr></thead><tbody></tbody></table>
            </div>
            <div class="chart-container">
              <canvas id="tipo-radicado-chart"></canvas>
              <table id="tipo-radicado-chart"><thead><tr><th>Tipo de Radicado</th><th>Total</th></tr></thead><tbody></tbody></table>
            </div>
            <div class="chart-container">
              <canvas id="tipo-solicitante-chart"></canvas>
              <table id="tipo-solicitante-chart"><thead><tr><th>Tipo de Solicitante</th><th>Total</th></tr></thead><tbody></tbody></table>
            </div>
            <div class="chart-container" id="tipo-producto-chart">
              <canvas id="tipo-producto-chart"></canvas>
              <table id="tipo-producto-chart"><thead><tr><th>Tipo de Producto</th><th>Total</th></tr></thead><tbody></tbody></table>
            </div>
            <div class="chart-container">
              <canvas id="canal-recepcion-chart"></canvas>
              <table id="canal-recepcion-chart"><thead><tr><th>Canal de Recepcion</th><th>Total</th></tr></thead><tbody></tbody></table>
            </div>
            <div class="chart-container">
              <canvas id="forma-recepcion-chart"></canvas>
              <table id="forma-recepcion-chart"><thead><tr><th>Forma de Recepcion</th><th>Total</th></tr></thead><tbody></tbody></table>
            </div>
          </div>'),
        'stringData' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->render->render($messageString),
          '#weight' => 2,
        ],
        '#attached' => [
          'drupalSettings' => [
            'pqrsdReports' => $data,
          ],
          'library' => [
            'findeter_pqrsd/admin_pages',
          ]
        ],
        '#cache' => [
          'max-age' => 0,
          'tags' => ['node_list:pqrsd'],
        ],
      ];

    }

    return $build;

  }

  /**
   * Funcion que genera la consulta y reconstruye la data para ser tratada.
   *
   * @return array
   *   Return array data
   */
  public function statisticsData($request) {

    // Arraglo con los meses del anio.
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

    // Settings para los siguientes campos y ser tratados para mostrar.
    $definitions = $this->fieldManager->getFieldDefinitions('node', 'pqrsd');

    // Tipo de solicitante.
    $settingsTipSoli = $definitions['field_pqrsd_tipo_solicitante']->getSettings()['allowed_values'];
    $tipoSolicitanteCount = [];

    foreach ($settingsTipSoli as $key => $value) {
      $tipoSolicitanteCount[$value] = 0;
    }

    // Nombre de producto.
    $settingsNameProduct = $definitions['field_pqrsd_nombre_producto']->getSettings()['allowed_values'];
    $tipoNameProductCount = [];

    foreach ($settingsNameProduct as $key => $value) {
      $tipoNameProductCount[$value] = 0;
    }

    // Forma de recepcion.
    $settingsFormRecep = $definitions['field_pqrsd_forma_recepcion']->getSettings()['allowed_values'];
    $formRecepCount = [];

    foreach ($settingsFormRecep as $key => $value) {
      $formRecepCount[$value] = 0;
    }

    // Inicializamos el arreglo.
    $data = [];

    // Realizamos una consulta para traer los nids de todo los nodos que perteneces al tipo de contenido pqrsd.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'pqrsd');

    // Se agrega los filtros de fecha para mostrar por periodos.
    if (!is_null($request->request->get('start_date'))) {
      $query->condition('created', [
        strtotime($request->request->get('start_date')['date']),
        strtotime($request->request->get('finished_date')['date'])
      ], 'BETWEEN');
    }
    else {
      $start_date = strtotime('first day of January', time());
      $finished_date = \Drupal::time()->getCurrentTime();
      $query->condition('created', [
        $start_date,
        $finished_date
      ], 'BETWEEN');
    }

    // Ejecutamos la consulta.
    $results = $query->execute();

    $nodeStorage = $this->entityTypeManager->getStorage('node');

    // Cargamos lo nid de nodos pqrsd.
    $nodeArray = $nodeStorage->loadMultiple($results);

    foreach ($nodeArray as $node) {

      /* ===== ===== Definimos valores para los graficos charts ===== ===== */

      // Registro mensula de radicados.
      $month = date('F', $node->get('created')->value);
      $month = $spanishMonths[$month];

      if (!isset($data['monthly'][$month])) {
        $data['monthly'][$month] = 0;
      }
      $data['monthly'][$month]++;

      // Tipo de radicado.
      $tipoRadicado = $node->get('field_pqrsd_tipo_radicado')->value;

      if (!isset($data['tipoRadicado'][$tipoRadicado])) {
        $data['tipoRadicado'][$tipoRadicado] = 0;
      }
      $data['tipoRadicado'][$tipoRadicado]++;

      // Tipo de solicitante.
      $tipoSolicitante = $node->get('field_pqrsd_tipo_solicitante')->value;

      foreach ($settingsTipSoli as $key => $value) {
        if ($tipoSolicitante == $key) {
          $tipoSolicitanteCount[$value]++;
          break;
        }
      }

      // Tipo de radicado.
      $tipoProducto = $node->get('field_pqrsd_nombre_producto')->value;

      foreach ($settingsNameProduct as $key => $value) {
        if ($tipoProducto == $key) {
          $tipoNameProductCount[$value]++;
          break;
        }
      }

      // Define data for "Canal recepción" field.
      $canalRecepcion = $node->get('field_pqrsd_canal_recepcion')->value;
      if (!isset($data['canalRecepcion'][$canalRecepcion])) {
        $data['canalRecepcion'][$canalRecepcion] = 0;
      }
      $data['canalRecepcion'][$canalRecepcion]++;

      // Define data for "Forma recepción" field.
      $formaRecepcion = $node->get('field_pqrsd_forma_recepcion')->value;

      foreach ($settingsFormRecep as $key => $value) {
        if ($formaRecepcion == $key) {
          $formRecepCount[$value]++;
          break;
        }
      }

    }

    // Tipos de solictante valores.
    $tipoSolicitanteValue = array_filter(
      $tipoSolicitanteCount,
      function ($item) {
        return $item > 0;
      });

    $data['tipoSolicitante'] = $tipoSolicitanteValue;

    // Nombre de productos.
    $tipoNameProductValue = array_filter(
      $tipoNameProductCount,
      function ($item) {
        return $item > 0;
      });

    $data['tipoProducto'] = $tipoNameProductValue;

    // Forma de recepcion.
    $formRecepValue = array_filter(
      $formRecepCount,
      function ($item) {
        return $item > 0;
      });

    $data['formaRecepcion'] = $formRecepValue;

    return $data;

  }

  /**
   * Se muestra resultados de PQRSD en la tabla reportes.
   *
   * @return mixed
   *   Coomand Ajax
   */
  public function resultsDataTable() {

    // Storage de tipo entidad node.
    $nodeStorage = $this->entityTypeManager->getStorage('node');

    // Agregamos u arreglo con los tipos de PQRSD.
    $tipoRadicados = [
      'Peticiones',
      'Quejas',
      'Reclamos',
      'Sugerencias',
      'Denuncias',
      'Total'
    ];

    // Inicializamos valores en 0.
    foreach ($tipoRadicados as $tr) {
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

    // Creamos una variable para almacenar la data.
    $data = [];

    // Realizamos una consulta para traer los nid de pqrsd y ser iterado por cada uno de ellos.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'pqrsd')
      ->execute();

    // Cargamos lo nid de nodos pqrsd.
    $nodeArray = $nodeStorage->loadMultiple($query);

    foreach ($nodeArray as $node) {

      // Define data for "Tipo radicado" field.
      $tipoRadicado = $node->get('field_pqrsd_tipo_radicado')->value;

      if (!isset($data['tipoRadicado'][$tipoRadicado])) {
        $data['tipoRadicado'][$tipoRadicado] = 0;
      }
      $data['tipoRadicado'][$tipoRadicado]++;

      /* ===== ===== TABLA DE RESULTADOS ===== ===== */
      $now = strtotime('today');
      $fOrange = strtotime($node->get('field_pqrsd_fecha_naranja')->value);
      $fRed = strtotime($node->get('field_pqrsd_fecha_roja')->value);
      $fAnswer = strtotime($node->get('field_pqrsd_fecha_respuesta')->value);

      // PQRSD cerradas/abiertas.
      $mResume[$tipoRadicado]['name'] = $tipoRadicado;
      $mResume[$tipoRadicado]['recibidas'] = $data['tipoRadicado'][$tipoRadicado];

      if ($node->get('field_pqrsd_respuesta')->value == '') {

        $mResume[$tipoRadicado]['por_resolver']++;

        if ($now > $fRed) {
          $mResume[$tipoRadicado]['vencidas']++;
        }
      }
      else {

        $mResume[$tipoRadicado]['cerrados']++;

        if ($node->get('field_pqrsd_respuesta_a_favor')->value == 'findeter') {
          $mResume[$tipoRadicado]['favor_entidad']++;
        }

        if ($node->get('field_pqrsd_respuesta_a_favor')->value == 'consumidor') {
          $mResume[$tipoRadicado]['favor_consumidor']++;
        }

        if ($fAnswer < $fRed) {
          $mResume[$tipoRadicado]['cerrado_a_tiempo']++;
        }

        if (strpos($node->get('field_pqrsd_respuesta')->value, 'PQRSD Trasladada a:')) {
          $mResume[$tipoRadicado]['trasladadas']++;
        }

      }

      if ($node->get('field_pqrsd_tipo_peticion')->value == 'publica') {
        $mResume[$tipoRadicado]['info_publica']++;
      }

    }

    foreach ($mResume as $tR => $values) {
      if ($tR != 'Total') {
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

    // Reconstruimos de nuevo la tabla para ser renderizada con la data.
    $buildTable = [
      'container' => [
        '#type' => 'container',
        '#attributes' => [
          'id' => "results-table-reports",
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

    // Creamos un evento ajax aync para mostrar la data, se remplace la tabla con los nuevos valores.
    $response = new AjaxResponse();

    $response->addCommand(new ReplaceCommand(
      '#results-table-reports',
      $this->render->render($buildTable),
    ));

    return $response;

  }

  /**
   * Se muestra resultados de PQRSD por anio.
   *
   * @return string
   *   Coomand Ajax
   */
  public static function resultsDataTableAnio($anio, $getQueryMonths = NULL) {

    BusinessDay::enable('Carbon\Carbon');
    Carbon::setHolidaysRegion('co');

    // Arraglo con los meses del anio.
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

    foreach ($spanishMonths as $key => $month) {
      $mTable[$key]['name'] = $month;
      $mTable[$key]['requests'] = 0;
      $mTable[$key]['attended'] = 0;
      $mTable[$key]['process'] = 0;
      $mTable[$key]['canceled'] = 0;
      $mTable[$key]['days_count'] = 0;
      $mTable[$key]['days_answer'] = 0;
    }

    // Storage de tipo entidad node.
    $nodeStorage = \Drupal::service('entity_type.manager')->getStorage('node');

    // Realizamos una consulta para traer los nid de pqrsd y ser iterado por cada uno de ellos.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'pqrsd')
      ->condition('created', [
        strtotime("01-01-$anio"),
        strtotime("31-12-$anio")
      ], 'BETWEEN')
      ->execute();

    // Cargamos lo nid de nodos pqrsd.
    $nodeArray = $nodeStorage->loadMultiple($query);

    // Recorremos la consulta y iteramos por cada una.
    foreach ($nodeArray as $key => $node) {
      $month = date('F', $node->get('created')->value);
      $mTable[$month]['requests']++;

      if ($node->get('field_pqrsd_respuesta')->value == '') {
        $mTable[$month]['process']++;
      }
      else {

        if (isset($node->get('field_pqrsd_fecha_respuesta')->getValue()[0]['value'])) {
          // $dateI = new \DateTime(date('Y-m-d', $node->get('created')->getValue()[0]['value']));
          // $dateF = new \DateTime($node->get('field_pqrsd_fecha_respuesta')->getValue()[0]['value']);
          // $days = $dateI->diff($dateF);

          // 86400 = 1 dia.
          // Se incrementa a la fecha de radicado.
          $dateI = $node->get('created')->getValue()[0]['value'] + 86400;
          $dateF = strtotime($node->get('field_pqrsd_fecha_respuesta')->getValue()[0]['value']);

          // Atendidas.
          $mTable[$month]['attended']++;

          // Contador de dias para cada fecha.
          $days = 0;

          for ($timeStamp = $dateI; $timeStamp <= $dateF; $timeStamp += 86400) {

            // Declaramos variabes para la fecha fesitvos.
            $day = date('d', $timeStamp);
            $mounth = date('m', $timeStamp);
            $year = date('Y', $timeStamp);

            // Obtenemos si es un dia vacacional o festivo.
            $holiDays = Carbon::parse("$year-$mounth-$day")->isHoliday();

            // Ni sabados ni Domingos.
            if (!in_array(date('N', $timeStamp), [6, 7])) {
              // Ni dias feriados.
              if (!$holiDays) {
                $days++;
              }
            }

          }

          // La siguientes variables es para realizar un promedio de dias de respuesta por mes.
          // Dias de respuesta.
          $mTable[$month]['days_count'] += $days;
          $days = $mTable[$month]['days_count'] / $mTable[$month]['attended'];
          $mTable[$month]['days_answer'] = round($days, 2);
        }
        else {
          $mTable[$month]['attended']++;
        }
      }

      if (!$node->isPublished()) {
        $mTable[$month]['canceled']++;
        $mTable[$month]['process']--;
      }

    }

    // Filtramos el resultado de meses que tengan mas de una solicitud.
    $mTable = array_filter(
      $mTable,
      function ($item) {
        return $item['requests'] > 0;
      });

    // Totales de cada item en tabla.
    $mTable['Total']['name'] = 'Total';
    $mTable['Total']['requests'] = 0;
    $mTable['Total']['attended'] = 0;
    $mTable['Total']['process'] = 0;
    $mTable['Total']['canceled'] = 0;

    if (count($mTable) > 1) {
      // Recogemos los totales del promedio de dias de respuesta en cada uno de los meses de array.
      $daysAvg = [];
      // Iteramos para acumular los totales.
      foreach ($mTable as $tR => $values) {

        unset($mTable[$tR]['days_count']);

        $mTable['Total']['requests'] += $values['requests'];
        $mTable['Total']['attended'] += $values['attended'];
        $mTable['Total']['process'] += $values['process'];
        $mTable['Total']['canceled'] += $values['canceled'];

        if (isset($values['days_answer'])) {
          array_push($daysAvg, $values['days_answer']);
        }
      }
      $mTable['Total']['days_answer'] = round(array_sum($daysAvg) / count($daysAvg), 2);
    }
    else {
      $mTable['Total']['days_answer'] = 0;
    }

    if (is_null($getQueryMonths)) {
      // Retornamos las variables en este caso datos de tipo json.
      return new JsonResponse([
        'status' => 200,
        'data' => $mTable,
        'method' => 'GET',
      ]);
    }
    else {
      return $mTable;
    }

  }

  /**
   * Enviar radicados de Quejas o Reclamos a la aplicacion API SMFC.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Retorna un json con el estado de la peticion.
   */
  public function sendDataSmfc() {

    $now = \Drupal::time()->getCurrentTime();

    $url = Url::fromRoute('dblog.overview');
    $link = Link::fromTextAndUrl(t('Registro de errores'), $url);

    $stateSmfcNid = $this->state->get('findeter_pqrsd.api_smfc_nid');

    if (!is_null($stateSmfcNid) && !empty($stateSmfcNid)) {

      $nid = [];
      $login = $this->apiSmfc->login();

      if ($login) {

        foreach ($stateSmfcNid as $key => $state) {

          $nid[$key] = [
            "nid" => $state['nid'],
            "title" => $state['title'],
            "created" => $state['created'],
            "smfc" => $state['smfc']
          ];

          if (date('Y-m-d', $now) !== date('Y-m-d', $state['created'])) {

            if ($state['smfc'] === FALSE) {

              $post = $this->apiSmfc->postComplaints($state['nid']);
              // $post = TRUE;

              if ($post) {
                $nid[$key]["smfc"] = TRUE;
                $this->messenge->addMessage('Se ha creado el ' . $state['title'] . ' en la aplicación SMFC.');
              }
              else {

                $nid[$key]["smfc"] = FALSE;
                $this->messenge->addError('No se ha podido enviar el ' . $state['title'] . 'a la aplicación SMFC. Ver ' . $link->toString() . ' para más información.');
              }

            }
            else {

              // Consultamos si hay informacion de usuarios para actualizar.
              $putUpdbUser = $this->apiSmfc->getUpdateInfoUserComplaints();

              if ($putUpdbUser) {
                $put = $this->apiSmfc->putComplaints($state['nid']);
                // $put = TRUE;

                if (!$put) {
                  $nid[$key]["smfc"] = TRUE;
                  $this->messenge->addWarning(Markup::create('No se ha podido actualizar el ' . $state['title'] . 'a la aplicación SMFC. Ver el estado de respuesta ó ' . $link->toString() . ' para más información.'));
                }
                else {
                  unset($nid[$key]);
                  $this->messenge->addMessage(Markup::create('Se ha actualizado y enviado el ' . $state['title'] . ' en la aplicación SMFC.'));
                }
              }
            }

          }
        }

        $this->state->set('findeter_pqrsd.api_smfc_nid', $nid);

        return new JsonResponse([
          'status' => 200,
          'method' => 'GET',
        ]);
      }
    }
  }

}
