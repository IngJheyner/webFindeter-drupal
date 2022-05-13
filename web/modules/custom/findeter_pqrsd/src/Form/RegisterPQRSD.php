<?php

namespace Drupal\findeter_pqrsd\Form;

use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\findeter_pqrsd\Manager\StepManager;
use Drupal\findeter_pqrsd\Step\StepsEnum;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\views\Ajax\ScrollTopCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\file\FileUsage\DatabaseFileUsageBackend;
use Drupal\Core\Entity\EntityTypeManager;


use Drupal\node\Entity\Node;
//use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;

use Drupal\findeter_pqrsd\Api\ApiSmfcInterface;

/**
 * Provides multi step ajax example form.
 *
 * @package Drupal\findeter_pqrsd\Form
 */
class RegisterPQRSD extends FormBase {
  use StringTranslationTrait;

    /**
   * Step Id.
   *
   * @var \Drupal\findeter_pqrsd\Step\StepsEnum
   */
  protected $stepId;

    /**
   * Multi steps of the form.
   *
   * @var \Drupal\findeter_pqrsd\Step\StepInterface
   */
  protected $step;

  /**
   * Step manager instance.
   *
   * @var \Drupal\findeter_pqrsd\Manager\StepManager
   */
  protected $stepManager;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

    /**
   * API SMFC servicio.
   *
   * @var \Drupal\findeter_pqrsd\Api\ApiSmfcInterface
   */
  protected $apiSmfc;

  /**
   * logger dblog.
   *
   * @var Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Crear archivo permanete en file.usage.
   *
   * @var Drupal\file\FileUsage\DatabaseFileUsageBackend $fileUsage
   */
  protected $fileUsage;
  
   /**
   * Propiedad de tipos de entidades.
   *
   * @var Drupal\Core\Entity\EntityTypeManager $entityTypeManager;
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(MessengerInterface $messenger, ApiSmfcInterface $api_smfc, StateInterface $state, DatabaseFileUsageBackend $file_usage, EntityTypeManager $entity_type_manager) {
    $this->stepId = StepsEnum::STEP_ZERO;
    // StepManager class needs those two arguments
    $this->stepManager = new StepManager($messenger);
    $this->messenger = $messenger;
    $this->apiSmfc = $api_smfc;
    $this->state = $state;
    $this->fileUsage = $file_usage;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('api.smfc'),
      $container->get('state'),
      $container->get('file.usage'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'register_pqrsd';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // get step from step manager.
    $this->step = $this->stepManager->getStep($this->stepId);

    // where all messages will printed
    $form['wrapper-messages'] = [
      '#type'       => 'container',
      '#attributes' => [
        'id' => 'messages-wrapper',
      ],
    ];

    // contain all form and listen ajax event to rebuild the entry form
    $form['wrapper'] = [
      '#type'       => 'container',
      '#attributes' => [
        'id' => 'form-wrapper'
      ],
    ];

    // title for each ajax step
    $textSteps[0] = 'Inicio';
    $textSteps[1] = 'Radicar solicitud';
    $textSteps[2] = 'Información básica del solicitante';
    $textSteps[3] = 'Información del producto';
    $textSteps[4] = 'Canal de respuesta';

    // build html output
    $htmlStep = '';
    foreach($textSteps as $nStep=>$textStep){
      $class = '';
      $classNumber = '';

      // set as active the previous and current step
      if($nStep <= $this->stepId){
        $class = 'class="flex-fill text-success font-weight-bold active"';
        $classNumber = 'class="number badge badge-success"';
      }else{
        $class = 'class="flex-fill"';
        $classNumber = 'class="number badge badge-secondary"';
      }

      $htmlStep .= '
        <li '.$class.'>
          <span '.$classNumber.'>'.($nStep+1).'</span>
          <span class="text">'.$textStep.'</span>
        </li>';
    }

    $form['wrapper']['wrapper-step'] = array(
      '#markup' => '<ul class="steps-counter list-unstyled d-lg-flex mt-3 mb-5">'.$htmlStep.'</ul>',
    );

    $form['wrapper']['content-fields'] = [
      '#type'       => 'container',
      /*'#attributes' => [
        'class' => ['rows']
      ],*/
    ];

    // Attach step form elements.
    $form['wrapper']['content-fields'] += $this->step->buildStepFormElements($this->stepManager->getAllSteps(),$form, $form_state);

    // Attach buttons.
    $form['wrapper']['actions']['#type'] = 'actions';
    $form['wrapper']['actions']['#attributes'] = ['class'=>['row','justify-content-center', 'my-5']];

    $buttons = $this->step->getButtons();
    
    foreach ($buttons as $button) {

      $form['wrapper']['actions'][$button->getKey()] = $button->build();

      if ($button->ajaxify()) {
        // Add ajax to button.
        $form['wrapper']['actions'][$button->getKey()]['#ajax'] = [
          'callback' => '::loadStep',
          'wrapper'  => 'form-wrapper',
          'effect'   => 'fade',
        ];
      }

      $callable = [$this, $button->getSubmitHandler()];
      if ($button->getSubmitHandler() && is_callable($callable)) {
        // Attach submit handler to button, so we can execute it later on..
        $form['wrapper']['actions'][$button->getKey()]['#submit_handler'] = $button->getSubmitHandler();
      }
    }

    $allSteps = $this->stepManager->getAllSteps();

    if(isset($allSteps[1])){

      if($allSteps[1]->getValues()['field_pqrsd_tipo_solicitante'] == 'anonimo'){

        $form['wrapper']['actions']['next']['#value'] = 'Enviar solicitud';
        $form['wrapper']['actions']['next']['#goto_step'] = 6;
        $form['wrapper']['actions']['next']['#submit_handler'] = 'submitValues';
        unset($form['wrapper']['actions']['next']['#ajax']);

      }

    }

    // fill fields with stored values
    if($this->step->getValues() != ''){
      foreach($this->step->getValues() as $field=>$value){
        if(isset($form['wrapper']['content-fields'][$field])){
          $form['wrapper']['content-fields'][$field]['#default_value'] = $value;
        }
      }
    }

    $form_state->setRebuild();
    $form['#attached']['library'][] = 'findeter_pqrsd/global-scripts';
    $form['#attributes']['class'][] = 'container';

    return $form;
  }


  /**
   * Ajax callback to load new step.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state interface.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response.
   */
  public function loadStep(array &$form, FormStateInterface $form_state) {

    $response = new AjaxResponse();
    $messages = $this->messenger->all();

    if (!empty($messages)) {
      // Form did not validate, get messages and render them.
      $messages = [
        '#theme'           => 'status_messages',
        '#message_list'    => $messages,
        '#status_headings' => [
          'status'  => $this->t('Status message'),
          'error'   => $this->t('Error message'),
          'warning' => $this->t('Warning message'),
        ],
      ];
      $response->addCommand(new HtmlCommand('#messages-wrapper', $messages));

      $this->messenger->deleteAll();

    }
    else {
      // Remove messages.
      $response->addCommand(new HtmlCommand('#messages-wrapper', ''));
    }


    if($this->stepId == 6){

      $steps = $this->stepManager->getAllSteps();

      $url = Url::fromRoute('findeter_pqrsd.confirm_register_pqrsd',['nid'=>$steps[4]->getValues()['new_nid']])->toString();
      //$url = Url::fromRoute('findeter_pqrsd.confirm_register_pqrsd',['operation'=>'create','nid'=>500])->toString();
      $response->addCommand(new \Drupal\Core\Ajax\RedirectCommand($url));

    }else{

      // Update Form.
      $response->addCommand(new ReplaceCommand('#form-wrapper',$form['wrapper']));

      //Scroll at the top of the form
      $response->addCommand(new ScrollTopCommand('#form-wrapper'));

    }

    return $response;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $values = [];
    foreach ($this->step->getFieldNames() as $name) {
      $values[$name] = $form_state->getValue($name);
    }
    $this->step->setValues($values);

    $allValues = $form_state->getValues();
    $steps = $this->stepManager->getAllSteps();
    if($steps!=''){
      foreach($steps as $step){
        $allValues += $step->getValues();
      }
    }

    $triggering_element = $form_state->getTriggeringElement();
    // Only validate if validation doesn't have to be skipped.
    // For example on "previous" button.
    if (empty($triggering_element['#skip_validation']) && $fields_validators = $this->step->getFieldsValidators()) {
      // Validate fields.
      foreach ($fields_validators as $field => $validators) {
        // Validate all validators for field.
        $field_value = $form_state->getValue($field);
        foreach ($validators as $validator) {
          if (!$validator->validates($field_value,$allValues)) {
            $form_state->setErrorByName($field, $validator->getErrorMessage());
          }
        }
      }
    }

  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Set step to navigate to.
    $triggering_element = $form_state->getTriggeringElement();

    // Save filled values to step. So we can use them as default_value later on.
    $values = [];
    foreach ($this->step->getFieldNames() as $name) {
      $values[$name] = $form_state->getValue($name);
    }
    // only for step zero
    if($this->step->getStep()===0){
      $values['field_pqrsd_tipo_radicado'] = $triggering_element['#value'];
    }

    $this->step->setValues($values);
    // Add step to manager.
    $this->stepManager->addStep($this->step);

    $this->stepId = $triggering_element['#goto_step'];

    // for anonimo request, jump step 2
    $allSteps = $this->stepManager->getAllSteps();
    if(isset($allSteps[1])){
      $valuesStep1 = $allSteps[1]->getValues();
      if($valuesStep1['field_pqrsd_tipo_solicitante'] == 'anonimo'){
        if($this->step->getStep() == 1){
          $this->stepId = 3;
        }

        if($this->step->getStep() == 3 && $triggering_element['#value']=='Volver'){
          $this->stepId = 1;
        }
      }

    }

    // If an extra submit handler is set, execute it.
    // We already tested if it is callable before.
    if (isset($triggering_element['#submit_handler'])) {
      $this->{$triggering_element['#submit_handler']}($form, $form_state);
    }

    $form_state->setRebuild(TRUE);
  }


  /**
   * Submit handler for last step of form.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state interface.
   */
  public function submitValues(array &$form, FormStateInterface $form_state) {

    //retrying all values
    $steps = $this->stepManager->getAllSteps();

    //define new node of content type
    $newRequest = Node::create(['type' => 'pqrsd']);
    $newRequest->set('title', 'User request - '.date('U'));

    //to retrive all values at one single array
    $values = [];
    //FileSotorage que se carga a la entidad de tipo file
    $fileStorageArray = [];
    foreach($steps as $step){
      foreach($step->getValues() as $field=>$value){

        //adding all values in the same array
        $values += $step->getValues();

        if($field == 'field_pqrsd_marketing'){
          if($value){
            $value = 'autorizacion_marketing';
          }else{
            $value = '';
          }
        }

        if($field == 'field_pqrsd_autorizacion'){
          $value = 'autorizacion_findeter';
        }

        if($value != ''){
          $newRequest->set($field, $value);
        }

        // store all files
        if($field == 'field_pqrsd_archivo'){

          $fileStorage = $this->entityTypeManager->getStorage('file');

          foreach($value as $key=>$fid){
            if (!empty($fid)) {

              /*$file = File::load($fid);
              $file->setPermanent();
              $file->save();*/
              $file = $fileStorage->load($fid);

              //Se agegra el file entidad al arreglo filestorage declarado antes del ciclo for
              $fileStorageArray[$key] = $file;
              
            }
          }
        }

      }
    }

    $numeroRadicado = generarNumeroRadicado($values['field_pqrsd_tipo_radicado']);

    // define title of node
    $newRequest->set('title', 'Radicado: '.$numeroRadicado.'.'.date('U'));

    // set "# radicado"
    /* Si es un radicado de tipo Queja, se antepone el codigo de entidad de findeter para SMFC 
    @author 3desarrollo===== ===== */
    if($values['field_pqrsd_tipo_radicado'] == 'Quejas' || 
    $values['field_pqrsd_tipo_radicado'] == 'Reclamos'){

      $numeroRadicado = $this->apiSmfc->getTipCodeEntity($numeroRadicado);
      $newRequest->set('field_pqrsd_numero_radicado', $numeroRadicado);

    }else{
      $newRequest->set('field_pqrsd_numero_radicado',$numeroRadicado);
    }    

    $config = $this->config('findeter_pqrsd.admin');

    $user = \Drupal\user\Entity\User::load($config->get('asign_user'));
    $newRequest->uid = $user->id();
    $newRequest->set('field_pqrsd_asignaciones', $user->getAccountName().' | '.$user->id().' | '.date('j/m/Y H:i:s'));

    // channel and way to recipt the PQRSD
    $newRequest->set('field_pqrsd_canal_recepcion', 'web');
    $newRequest->set('field_pqrsd_forma_recepcion', 'electronico');

    // define date of answer
    $datesConfigure = defineDatesSemaphore($values);
    $newRequest->set('field_pqrsd_fecha_roja', $datesConfigure['red']);
    $newRequest->set('field_pqrsd_fecha_naranja', $datesConfigure['orange']);

    //Instancia de recepcion @author 3desarrollo
    //Por defecto 2. Entidad
    $newRequest->set('field_pqrsd_instance_reception', 2);

    /* set "# radicado"
    $newRequest->set('field_pqrsd_numero_radicado',$numeroRadicado);*/

    $newRequest->enforceIsNew();
    $newRequest->save();

    /* Se agrega como archivos gestionados a file.usage  ===== ===== */
    foreach($fileStorageArray as $file){

      $this->fileUsage->add($file, 'findeter_pqrsd', 'node', $newRequest->id());
           
    }
     
    // send email
    if(isset($values['field_pqrsd_email']) && $values['field_pqrsd_email'] != ''){

      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'findeter_pqrsd';
      $key = 'registered_pqrsd';
      $to = $values['field_pqrsd_email'];

      /*$url = Url::fromRoute('findeter_pqrsd.status_pqrsd');
      $statusLink = Link::fromTextAndUrl('ingresar', $url);
      $statusLink = $statusLink->toRenderable();*/

      $mailBody[] = '<p>Reciba un cordial saludo de parte de Findeter.</p>';

      $mailBody[] = '<p>De antemano queremos agradecerle por haberse puesto en contacto con nosotros a traves del sistema de atención al usuario. Su opinión es muy importante para nosotros.</p>';

      $mailBody[] = '<p>Le informamos que su solicitud ha sido registrada satisfactoriamente con el código de radicado:<br><strong>'.$numeroRadicado.'</strong></p>';
      
      $mailBody[] = '<p>Con este código podrá <a href="https://www.findeter.gov.co/estado-pqrsd">ingresar</a> para consultar el estado de la misma y si es el caso ampliar o enviar información adicional</p>';

      $mailBody[] = '<hr>';
      $mailBody[] = 'Cordialmente,';
      $mailBody[] = 'Vicepresidencia comercial - Servicio al cliente';
      $mailBody[] = 'Findeter';
      $mailBody[] = '<a href="https://www.findeter.gov.co" target="_blank"><img onerror="this.remove();" alt="Findeter" src="https://www.findeter.gov.co/sites/default/files/webfinde/images/encabezado/logo.png" width="300" height="150"></a>';

      $params['message'] = implode('<br>',$mailBody);

      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = true;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

      if($result['result'] !== true){
        \Drupal::messenger()->addError('Ocurrió un problema al enviar el correo.');
      }
    }

    sendNotificationAsign($user,$newRequest);

    // store the nid value of new node created
    $values['new_nid'] = $newRequest->id();

    $values['pqrsd_numero_radicado'] = $numeroRadicado;

    $this->step->setValues($values);

    /* ============================================
    Se envia la peticion de PQRSD al sistema SMFC
    siempre y cuando el tipo de radicado sea una
    Queja o Reclamo.
    @author 3ddesarrollo
    =============================================== */
    if($values['field_pqrsd_tipo_radicado'] == 'Quejas' || 
    $values['field_pqrsd_tipo_radicado'] == 'Reclamos'){

      /* Se guarda los nid como variables de estado para que despues
      sea registrado en la API SMFC. ==== ====== */
      $nid = $this->state->get('findeter_pqrsd.api_smfc_nid');
      
      if(is_null($nid)){

        $this->state->set('findeter_pqrsd.api_smfc_nid', [
          [
          "nid" => $values['new_nid'],
          "title" => $newRequest->getTitle(),
          "created" => $newRequest->getCreatedTime(),
          "smfc" => FALSE,
          ]
        ]);

      }else{
        $nid[] = [
        "nid" => $values['new_nid'],
        "title" => $newRequest->getTitle(),
        "created" => $newRequest->getCreatedTime(),
        "smfc" => FALSE,
        ];
        
        $this->state->set('findeter_pqrsd.api_smfc_nid', $nid);

      }
    }

  }

}
