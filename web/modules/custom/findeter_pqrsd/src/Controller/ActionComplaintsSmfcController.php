<?php
namespace Drupal\findeter_pqrsd\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\findeter_pqrsd\Api\ApiSmfcInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\State\StateInterface;

/**
 * Returns responses for findeter_pqrsd routes.
 */
class ActionComplaintsSmfcController extends ControllerBase {

  /**
   * @var Drupal\Core\State\StateInterface state;
   */
  protected $state;

  public function __construct(StateInterface $state){
    $this->state = $state;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('state')
    );
  }

  /**
   * Se consulta e importa las quejas desde la API SMFC
   */
  public function consultComplaints():object {

    /* ============================================
    Se consulta si existe datos para registrar en PQRSD
    =============================================== */
    $data = $this->state->get('findeter_pqrsd.api_smfc_data');

    if(is_null($data) || empty($data)){

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
            'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::callbackGetComplaints',[]
          ],
          [
            'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::callbackAckGetComplaints',[]
          ],
          [
            'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::callbackSaveComplaints',[]
          ],
        ],
        'finished' => 'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::batch_finished',[$data],
        'title' => t('Smart Supervison API SMFC'),
        'init_message' => t('Starting import.'),
        'progress_message' => t('processing @current out of @total.'),
        'error_message' => t('An error was encountered importing the data.'),
        //'file' => drupal_get_path('module', 'batch_example') . '/batch_example.inc',
      ];

    }else{

      /* ============================================
      Construimos un batch.inic para enviar varios procesos por lotes. callaback
      3. SaveComplaints
      =============================================== */    
      $batch = [
        'title' => t('Exporting'),
        'operations' => [
          [
            'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::callbackSaveComplaints',[$data]
          ],
        ],
        'finished' => 'Drupal\findeter_pqrsd\Controller\ActionComplaintsSmfcController::batch_finished',[],
        'title' => t('Smart Supervison API SMFC'),
        'init_message' => t('Starting import.'),
        'progress_message' => t('processing @current out of @total.'),
        'error_message' => t('An error was encountered importing the data.'),
        //'file' => drupal_get_path('module', 'batch_example') . '/batch_example.inc',
      ];

    }

    batch_set($batch);//Setter del batch
    return batch_process("admin/reportes-pqrsd");//Procesar y renderizar al terminar el batch
  }

  /**
   * 1. Proceso GetComplaints
   * Importar la data desde la API SMFC
   * @param $context
   * @return Drupal\findeter_pqrsd\Api\ApiSmfcInterface
   */
  public static function callbackGetComplaints(&$context){
    return \Drupal::service('api.smfc')->getComplaints($context);
  }

    /**
   * 2. Proceso ACK
   * Enviar la data importada hacia la API SMFC, momento 1 ack
   * Este se da siempre y cuando el primer proceso sea exitoso.
   * @param $context
   * @return Drupal\findeter_pqrsd\Api\ApiSmfcInterface
   */
  public static function callbackAckGetComplaints(&$context){
    return \Drupal::service('api.smfc')->ackComplaints($context);
  }

  /**
   * 3. Proceso Save
   * Gurar la data importada desde la API SMFC como un tipo de radicado Queja
   * @param $context
   */
  public static function callbackSaveComplaints($data, &$context){

    //Inicializamos valores
    if (!isset($context['sandbox']['progress'])) {
      $context['sandbox']['progress'] = 0;
      //$context['sandbox']['current_node'] = 0;
      $context['sandbox']['max'] = 5;
    }

    foreach ($data as $key => $value) {

      //Se agregan los valores a la variable resultado para ser mostrados.
      $context['results'][] = $value;

      //Actualizar el progreso de informacion.
      $context['sandbox']['progress']++;
      //$context['message'] = t('API SMFC: Importando @num quejas para findeter', ['@num' => count($context['results'])]);
    }

    // Informar al motor por lotes que no hemos terminado,
      // y proporcionar una estimación del nivel de finalización que alcanzamos.
      if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
        $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
    }
  }

  /**
   * Batch 'finished' callback
   */
  public static function batch_finished($success, $results, $operations):void {
    if ($success) {

      //Proceso 1
      if(isset($results['error'])){//Proceso 1

        if(isset($results['error']['error_get'])){
          \Drupal::messenger()->addMessage($results['error']['error_get'], 'error');
        }

        if(isset($results['error']['error_ack']['error'])){//Proceso 2 error
          \Drupal::messenger()->addMessage($results['error']['error_ack']['error'], 'error');
        }

        if(isset($results['error']['error_ack']['warning'])){//Proceso 2 error desde proceso 1
          \Drupal::messenger()->addMessage($results['error']['error_ack']['warning'], 'warning');
        }

      }else{

        if(isset($results['empty'])){

          \Drupal::messenger()->addMessage($results['empty'], 'info');

        }else{
          \Drupal::messenger()->addMessage("Terminado", 'info');

        }

      }
      
    }
    else {
      // An error occurred.
      // $operations contains the operations that remained unprocessed.
      $error_operation = reset($operations);
      $message = t('An error occurred while processing %error_operation with arguments: @arguments', array('%error_operation' => $error_operation[0], '@arguments' => print_r($error_operation[1], TRUE)));
      \Drupal::messenger()->addMessage($message, 'error');
    }
    
  }

}
