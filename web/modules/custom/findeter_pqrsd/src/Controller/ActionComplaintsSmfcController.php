<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Returns responses for findeter_pqrsd routes.
 */
class ActionComplaintsSmfcController extends ControllerBase {

  /**
   * Se consulta e importa las quejas desde la API SMFC.
   *
   * @return object
   *   Se retorna un objecto bashc inic.
   */
  public function consultComplaints():object {
    /* ============================================
    Se consulta si existe datos para registrar o consultar en PQRSD
    =============================================== */
    $data = \Drupal::service('state')->get('findeter_pqrsd.api_smfc_data');

    if (is_null($data) || empty($data)) {

      /* ============================================
      Construimos un batch.inic para enviar varios procesos por lotes. callaback
      1. GetComplaints
      2. ACK
      3. SaveComplaints
      =============================================== */
      $batch = [
        'title' => t('Exporting'),
        'operations' => [
          [
            'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::callbackGetComplaints', []
          ],
          [
            'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::callbackAckGetComplaints', []
          ],
          [
            'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::callbackSaveComplaints', []
          ],
        ],
        'finished' => 'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::batchFinished', [],
        'title' => t('Smart Supervison API SMFC'),
        'init_message' => t('Starting import.'),
        'progress_message' => t('processing @current out of @total.'),
        'error_message' => t('An error was encountered importing the data.'),
        // 'file' => drupal_get_path('module', 'batch_example') . '/batch_example.inc',
      ];

    }
    else {

      /* ============================================
      Construimos un batch.inic para enviar varios procesos por lotes. callaback
      3. SaveComplaints
      =============================================== */
      $batch = [
        'title' => t('Exporting'),
        'operations' => [
          [
            'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::callbackSaveComplaints', []
          ],
        ],
        'finished' => 'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::batchFinished', [],
        'title' => t('Smart Supervison API SMFC'),
        'init_message' => t('Starting import.'),
        'progress_message' => t('processing @current out of @total.'),
        'error_message' => t('An error was encountered importing the data.'),
        // 'file' => drupal_get_path('module', 'batch_example') . '/batch_example.inc',
      ];

    }

    batch_set($batch);
    // Setter del batch.
    return batch_process("admin/reportes-pqrsd");
    // Procesar y renderizar al terminar el batch.
  }

  /**
   * Proceso GetComplaints 1.
   *
   * Importar la data desde la API SMFC.
   *
   * @param mixed $context
   *   Context.
   *
   * @return Drupal\findeter_pqrsd\Api\ApiSmfcInterface
   *   Method.
   */
  public static function callbackGetComplaints(&$context) {
    return \Drupal::service('api.smfc')->getComplaints($context);
  }

  /**
   * Proceso ACK 2.
   *
   * Enviar la data importada hacia la API SMFC, momento 1 ack
   * Este se da siempre y cuando el primer proceso sea exitoso.
   *
   * @param mixed $context
   *   Context.
   *
   * @return Drupal\findeter_pqrsd\Api\ApiSmfcInterface
   *   Method.
   */
  public static function callbackAckGetComplaints(&$context) {
    return \Drupal::service('api.smfc')->ackComplaints($context);
  }

  /**
   * Proceso Save 3.
   *
   * Guardar la data importada desde la API SMFC como un tipo de radicado Queja.
   *
   * @param mixed $context
   *   Context.
   */
  public static function callbackSaveComplaints(&$context) {

    if (!isset($context['results']['empty']) &&
        !isset($context['results']['error'])) {

      /* ============================================
      Tipo de entidad para guardar el contenido pqrsd
      =============================================== */
      $nodeStorage = \Drupal::service('entity_type.manager')->getStorage('node');

      /* ============================================
      Tipo campo manager para traer las opciones predefinidas
      del campo.
      =============================================== */
      $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'pqrsd');

      /* ============================================
      Tipo de entidad para archivos
      =============================================== */
      $fileStorage = \Drupal::service('entity_type.manager')->getStorage('file');

      /* ============================================
      Tipo de usabilidad del archivo en el usage
      =============================================== */
      $fileUsage = \Drupal::service('file.usage');

      /* ============================================
      Obtenemos la variable de estados
      =============================================== */
      $state = \Drupal::service('state');
      $data = $state->get('findeter_pqrsd.api_smfc_data');
      $dataNid = $state->get('findeter_pqrsd.api_smfc_nid');

      /* ============================================
      Logger
      =============================================== */
      $logger = \Drupal::service('logger.factory');

      // Inicializamos valores.
      if (!isset($context['sandbox']['progress'])) {
        $context['sandbox']['progress'] = 0;
        $context['results'] = [];
        $context['sandbox']['max'] = count($data);
      }

      foreach ($data as $value) {

        if (!isset($value['radicado_update']) && $value['desistimiento_queja'] === 2) {

          try {

            $newRequest = $nodeStorage->create(['type' => 'pqrsd']);

            // Titulo y numero de radicado.
            if (!isset($value['codigo_queja'])) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node title. Complaint code: @code", ['@code' => $value['codigo_queja']]));
              // Ocurrió un error al guardar el título del nodo. Código de queja:

            }
            else {
              $dateU = date('U');
              $newRequest->set('title', "Radicado: {$value['codigo_queja']}.$dateU");
              $newRequest->set('field_pqrsd_numero_radicado', $value['codigo_queja']);
            }

            // Fecha de creacion.
            if (!isset($value['fecha_creacion'])) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node date. Complaint code: @code", ['@code' => $value['codigo_queja']]));
              // Ocurrió un error al guardar la fecha del nodo. Código de queja:

            }
            else {
              $timeCreated = strtotime(new DrupalDateTime($value['fecha_creacion']));
              $newRequest->set('created', $timeCreated);
            }

            // Tipo de radicado.
            $newRequest->set('field_pqrsd_tipo_radicado', 'Quejas');

            // Tipo de solicitante, tipo de identificacion, numero de identificacion o NIT.
            if ((!isset($value['tipo_persona']) && !isset($value['tipo_id_CF'])) || (!isset($value['numero_id_CF']) && !is_numeric(['numero_id_CF']))) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node tipo_solicitante, tipo_id_CF o numero_id_CF. Complaint code: @code", ['@code' => $value['codigo_queja']]));
              // Ocurrió un error al guardar la fecha del nodo. Código de queja:

            }
            else {

              $newRequest->set('field_pqrsd_tipo_solicitante', $value['tipo_persona']);

              if ($value['tipo_id_CF'] == 3) {

                $newRequest->set('field_pqrsd_nit', $value['numero_id_CF']);

              }
              else {

                // Numero de identificacion.
                $newRequest->set('field_pqrsd_numero_id', $value['numero_id_CF']);

                // Tipo de identificacion o NIT.
                $newRequest->set('field_pqrsd_tipo_documento', $value['tipo_id_CF']);

              }
            }

            // Sexo.
            if (isset($value['sexo'])) {
              $newRequest->set('field_pqrsd_sexo', $value['sexo']);
            }

            // lgbtiq.
            if (isset($value['lgbtiq'])) {

              $newRequest->set('field_pqrsd_lgtbi', $value['lgbtiq']);

            }

            // Nombre(Solo se agrega al campo <<primer nombre>>.
            if (!isset($value['nombres'])) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node nombres. Complaint code: @code", ['@code' => $value['codigo_queja']]));
              // Ocurrió un error al guardar la fecha del nodo. Código de queja:

            }
            else {

              if ($value['tipo_id_CF'] == 3) {
                $newRequest->set('field_pqrsd_razon_social', $value['nombres']);
              }
              else {
                $newRequest->set('field_pqrsd_primer_nombre', $value['nombres']);
              }

            }

            // Pais.
            if (!isset($value['codigo_pais'])) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node codigo_pais. Complaint code: @code", ['@code' => $value['codigo_queja']]));
              // Ocurrió un error al guardar el pais del nodo. Código de queja:

            }
            else {

              $query = \Drupal::entityQuery('taxonomy_term');
              $query->condition('field_code_iso_countries', $value['codigo_pais']);
              $tids = $query->execute();

              $newRequest->set('field_paises_pqrsd', $tids);

            }

            // Departamento.
            if (!is_null($value['departamento_cod'])) {

              $query = \Drupal::entityQuery('taxonomy_term');
              $query->condition('field_code_dane_dpto', $value['departamento_cod']);
              $tids = $query->execute();

              $newRequest->set('field_pqrsd_departamento', $tids);

            }

            // Municipio.
            if (!is_null($value['municipio_cod'])) {

              $query = \Drupal::entityQuery('taxonomy_term');
              $query->condition('field_code_dane_dpto', $value['municipio_cod']);
              $tids = $query->execute();

              $newRequest->set('field_pqrsd_municipio', $tids);

            }

            // Telefono.
            if (!is_null($value['telefono'])) {
              $newRequest->set('field_pqrsd_telefono', $value['telefono']);
            }

            // Correo.
            if (!isset($value['correo'])) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node correo. Complaint code: @code", ['@code' => $value['codigo_queja']]));
              // Ocurrió un error al guardar la fecha del nodo. Código de queja:

            }
            else {
              $newRequest->set('field_pqrsd_email', $value['correo']);
            }

            // Direccion.
            if (isset($value['direccion'])) {

              $newRequest->set('field_pqrsd_direccion', $value['direccion']);

            }

            // Producto.
            $itemsProduct = $definitions['field_pqrsd_nombre_producto']->getSetting('allowed_values');

            if (!isset($value['producto_cod']) || !array_key_exists($value['producto_cod'], $itemsProduct)) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node producto_cod, The most possible cause is that the import product code @code_product does not match the code registered in the pqrsd system. Complaint code: @code",
              [
                '@code_product' => $value['producto_cod'],
                '@code' => $value['codigo_queja']
              ]));
              // Ocurrió un error al guardar la fecha del nodo, La causa mas posible es que el codigo x del producto de importacion no coincida con el codigo registrado en el sistema pqrsd. Código de queja:

            }
            else {
              $newRequest->set('field_pqrsd_nombre_producto', $value['producto_cod']);
            }

            // Descripcion de solicitud.
            if (!isset($value['texto_queja'])) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node texto_queja. Complaint code: @code", ['@code' => $value['codigo_queja']]));
              // Ocurrió un error al guardar la fecha del nodo. Código de queja:

            }
            else {
              $newRequest->set('field_pqrsd_descripcion', $value['texto_queja']);
            }

            // archivos.
            // FileSotorage que se carga a la entidad de tipo file.
            $fileStorageArray = [];

            if (isset($value['anexo_queja']) && isset($value['archivos_fid']) && $value['anexo_queja']) {

              foreach ($value['archivos_fid'] as $keyF => $fid) {
                $newRequest->field_pqrsd_archivo[] = $fid;

                // Cargamos por completo la entidad del archivo.
                $file = $fileStorage->load($fid);

                // Se agegra el file entidad al arreglo filestorage declarado antes del ciclo for.
                $fileStorageArray[$keyF] = $file;
              }
            }

            // Macro motivo.
            $itemsMotive = $definitions['field_pqrsd_macro_motivo']->getSetting('allowed_values');

            if (!isset($value['macro_motivo_cod']) || !array_key_exists($value['macro_motivo_cod'], $itemsMotive)) {
              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node macro_motivo_cod, The most possible cause is that the import motive code @code_motive does not match the code registered in the pqrsd system. Complaint code: @code",
              [
                '@code_motive' => $value['macro_motivo_cod'],
                '@code' => $value['codigo_queja']
              ]));
              // Ocurrió un error al guardar la fecha del nodo, La causa mas posible es que el codigo del motivo de importacion no coincida con el codigo registrado en el sistema pqrsd. Código de queja:
            }
            else {
              $newRequest->set('field_pqrsd_macro_motivo', $value['macro_motivo_cod']);
            }

            // Canal.
            if (isset($value['canal_cod'])) {
              $newRequest->set('field_pqrsd_canal', $value['canal_cod']);
            }

            // Canal de recepcion.
            $newRequest->set('field_pqrsd_canal_recepcion', 'web');

            // Condicion especial.
            if (isset($value['condicion_especial'])) {
              $newRequest->set('field_pqrsd_condicion_especial', $value['condicion_especial']);
            }

            // Tutela.
            if (!isset($value['tutela'])) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node tutela. Complaint code: @code", ['@code' => $value['codigo_queja']]));
              // Ocurrió un error al guardar la fecha del nodo. Código de queja:

            }
            else {
              $newRequest->set('field_pqrsd_tutela', $value['tutela']);
            }

            // Entes de control.
            if (!is_null($value['ente_control'])) {
              $newRequest->set('field_pqrsd_entes_control', $value['ente_control']);
            }

            // Desistimiento queja.
            if (!isset($value['desistimiento_queja'])) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node desistimiento_queja. Complaint code: @code", ['@code' => $value['codigo_queja']]));
              // Ocurrió un error al guardar la fecha del nodo. Código de queja:

            }
            else {
              $newRequest->set('field_pqrsd_desistimiento_queja', $value['desistimiento_queja']);
            }

            // Queja express.
            if (!isset($value['queja_expres'])) {

              // Se agregan los valores a la variable resultado para ser mostrados.
              $context['results']['error_save'][] = $value;
              // Actualizar el progreso de informacion.
              $context['sandbox']['progress']++;

              throw new \Exception(t("An error occurred while saving the node queja_expres. Complaint code: @code", ['@code' => $value['codigo_queja']]));
              // Ocurrió un error al guardar la fecha del nodo. Código de queja:

            }
            else {
              $newRequest->set('field_pqrsd_queja_expres', $value['queja_expres']);
            }

            // Medio de respuesta.
            $newRequest->set('field_pqrsd_medio_respuesta', 'email');

            // Forma de recepcion.
            $newRequest->set('field_pqrsd_forma_recepcion', 1);

            // Autorizacion.
            $newRequest->set('field_pqrsd_autorizacion', 'autorizacion_findeter');

            // Marketing.
            $newRequest->set('field_pqrsd_marketing', 'autorizacion_marketing');

            // Asignaciones.
            $nowTime = date('d-m-Y H:m:s', strtotime(new DrupalDateTime()));
            $newRequest->set('field_pqrsd_asignaciones', "3desarrollo | 1 | $nowTime");

            // Define la fecha naranja y de vencimiento.
            $datesConfigure = defineDatesSemaphore($value);
            $newRequest->set('field_pqrsd_fecha_roja', $datesConfigure['red']);
            $newRequest->set('field_pqrsd_fecha_naranja', $datesConfigure['orange']);

            // Estado con la API SMFC.
            $newRequest->set('field_pqrsd_api_smfc', 'recibido');

            $newRequest->enforceIsNew(TRUE);
            $newRequest->save();
            // $id = $newRequest->id();

            // Se agregan los valores a la variable resultado para ser mostrados.
            $context['results']['save'][] = [
              "nid" => $newRequest->id(),
              "title" => $newRequest->getTitle(),
              "created" => $newRequest->getCreatedTime(),
              "smfc" => TRUE,
              "put_files" => FALSE,
            ];

            // Actualizar el progreso de informacion.
            $context['sandbox']['progress']++;

            /* Se agrega como archivos gestionados a file.usage  ===== ===== */
            foreach ($fileStorageArray as $file) {
              $fileUsage->add($file, 'findeter_pqrsd', 'node', $newRequest->id());
            }

          }
          catch (\Exception $e) {

            $logger->get('API SMFC')->error("Registro data: Codigo queja %code Mensaje: %message",
            [
              '%code' => $value['codigo_queja'],
              '%message' => $e->getMessage()
            ]);
            // Registar en dblog.

            \Drupal::messenger()->addMessage($e->getMessage(), 'error');
            // Ocurrio un error en uno de los registros de la importacion, para mayor informacion ponerse en contacto con el administrador de sistema.

            // Guardar solo la data que ha ocasionado el error, verificacion y volver a cargar.
            $state->set('findeter_pqrsd.api_smfc_data', $context['results']['error_save']);
          }

        }
        else {

          if ($value['desistimiento_queja'] === 1) {

            // Cargamos quejas o reclamos con el numero de radicado.
            $pqrsds = $nodeStorage->loadByProperties([
              'type' => 'pqrsd',
              'field_pqrsd_numero_radicado' => $value['codigo_queja'],
            ]);

            if (!empty($pqrsds)) {

              foreach ($pqrsds as $key => $pqrsd) {

                // Se guarda un mensaje en la respuesta que indique el desistimento de la queja.
                $pqrsd->set('field_pqrsd_respuesta', 'La Queja o Reclamo ha sido desistida por parte del usuario en SMFC. desistimiento_queja: 1');

                // Se quita de la variable de estado para no ser enviado a la SMFC.
                $dataNidKey = array_search($key, array_column($dataNid, 'nid'));
                unset($dataNid[$dataNidKey]);
                $state->set('findeter_pqrsd.api_smfc_nid', $dataNid);
                // Guardar el pqrsd.
                $pqrsd->save();
              }
            }
          }
          // Si el codigo de radicado viene por data pero no ha sido procesado para guardar, es por que en algun proceso se ha llamado para ser actualizado.
          // Actualizar el progreso de informacion.
          $context['sandbox']['progress']++;

          $context['message'] = t('<strong>Findeter:</strong> La queja o reclamo @code ha sido actualizada.', ['@code' => $value['codigo_queja']]);
          \Drupal::messenger()->addMessage(t('<strong>Findeter: La queja o reclamo @code ha sido actualizada..</strong>', ['@code' => $value['codigo_queja']]));
          // Se ha actualizado la Queja con No. de radicado.

        }

      }

      /* Se guarda los nid como variables de estado para que despues
      sea ACTUALIZADO en la API SMFC. ==== ====== */
      $nid = $state->get('findeter_pqrsd.api_smfc_nid');

      if (is_null($nid) || empty($nid)) {

        isset($context['results']['save']) ? $state->set('findeter_pqrsd.api_smfc_nid', $context['results']['save']) : $state->set('findeter_pqrsd.api_smfc_nid', NULL);

      }
      else {

        if (isset($context['results']['save'])) {

          $nid = array_merge($nid, $context['results']['save']);
          $state->set('findeter_pqrsd.api_smfc_nid', $nid);

        }
        else {
          $state->set('findeter_pqrsd.api_smfc_nid', $nid);
        }

      }

      // Si no presenta ningun error, se elimna del la variable de estado data.
      if (!isset($context['results']['error_save'])) {
        $state->set('findeter_pqrsd.api_smfc_data', NULL);
      }

      if (isset($context['results']['save']) && isset($context['results']['error_save'])) {

        $numMessage = count($context['results']['save']) + count($context['results']['error_save']);
        $context['message'] = t('<strong>Findeter: </strong>:( Oops! something happened: @num complaints with @num_err errors in the log.',
        [
          '@num' => $numMessage,
          '@num_err' => count($context['results']['error_save'])
        ]);

      }
      else {

        if (isset($context['results']['save'])) {
          $context['message'] = t('<strong>Findeter: </strong>:) Good! @num complaints have been registered as pqrsd in the system.', ['@num' => count($context['results']['save'])]);
        }

        if (isset($context['results']['error_save'])) {
          $context['message'] = t('<strong>Findeter: </strong>:( Whoops! Something happened! Errors have occurred in all logs.');
        }
      }

      // Informar al motor por lotes que no hemos terminado,
      // y proporcionar una estimación del nivel de finalización que alcanzamos.
      if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
        $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
      }

    }
    else {

      $context['finished'] = 1;
      $context['message'] = t('<strong>Findeter: </strong>Ending...');

    }
  }

  /**
   * Batch 'finished' callback.
   */
  public static function batchFinished($success, $results, $operations):void {
    if ($success) {

      // Proceso 1.
      if (isset($results['error'])) {

        if (isset($results['error']['error_get'])) {
          \Drupal::messenger()->addMessage($results['error']['error_get'], 'error');
        }

        if (isset($results['error']['error_ack']['error'])) {
          \Drupal::messenger()->addMessage($results['error']['error_ack']['error'], 'error');
        }

        if (isset($results['error']['error_ack']['warning'])) {
          \Drupal::messenger()->addMessage($results['error']['error_ack']['warning'], 'warning');
        }

      }
      else {

        if (isset($results['empty'])) {

          \Drupal::messenger()->addMessage($results['empty'], 'info');

        }
        else {

          \Drupal::messenger()->addMessage(t("The import and registration process is finished:<br><br>1: Errors: <strong> @num_error </strong><br>2: Registered: <strong> @num_reg </strong><br>Note: If you see errors greater than zero, contact the system administrator for more information.",
          [
            '@num_error' => isset($results['error_save']) ? count($results['error_save']) : 0,
            '@num_reg' => isset($results['save']) ? count($results['save']) : 0
          ]));
          // El proceso de importación y registro ha finalizado:
          // 1: Errores: @num_error
          // 2: Registrados: @num_reg
          // Nota: Si visualiza errores mayores a cero, contactarse con el administrador del sistema para mas información.

        }

      }

    }
    else {
      // An error occurred.
      // $operations contains the operations that remained unprocessed.
      $error_operation = reset($operations);
      $message = t('An error occurred while processing %error_operation with arguments: @arguments',
      [
        '%error_operation' => $error_operation[0],
        '@arguments' => print_r($error_operation[1],
        TRUE)
      ]);
      \Drupal::messenger()->addMessage($message, 'error');
    }

  }

}
