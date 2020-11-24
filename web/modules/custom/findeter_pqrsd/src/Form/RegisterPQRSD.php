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


use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;

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
   * {@inheritdoc}
   */
  public function __construct(MessengerInterface $messenger) {
    $this->stepId = StepsEnum::STEP_ZERO;
    // StepManager class needs those two arguments
    $this->stepManager = new StepManager($messenger);
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      //$container->get('logger.factory')->get('registerPQRSD')
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
      
      // set as active the previous and current step
      if($nStep <= $this->stepId){
        $class = 'class="active"';
      }

      $htmlStep .= '
        <li '.$class.'>
          <span class="number">'.($nStep+1).'</span>
          <span class="text">'.$textStep.'</span>
        </li>';
    }

    $form['wrapper']['wrapper-step'] = array(
      '#markup' => '<ul class="steps-counter">'.$htmlStep.'</ul>',
    );
    
    $form['wrapper']['content-fields'] = [
      '#type'       => 'container',
      '#attributes' => [
        'class' => ['row']
      ],
    ];

    // Attach step form elements.
    $form['wrapper']['content-fields'] += $this->step->buildStepFormElements($this->stepManager->getAllSteps(),$form, $form_state);

    // Attach buttons.
    $form['wrapper']['actions']['#type'] = 'actions';
    $form['wrapper']['actions']['#attributes'] = ['class'=>['col-12','d-flex','justify-content-center']];

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

    // Update Form.
    $response->addCommand(new ReplaceCommand('#form-wrapper',$form['wrapper']));

    //Scroll at the top of the form
    $response->addCommand(new ScrollTopCommand('#form-wrapper'));

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

    $numeroRadicado = generarNumeroRadicado();

    // define title of node
    $newRequest->set('title', 'Radicado: '.$numeroRadicado.'.'.date('U'));

    // set "# radicado"
    $newRequest->set('field_pqrsd_numero_radicado',$numeroRadicado);

    //to retrive all values at one single array
    $values = []; 
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
          foreach($value as $fid){
            if (!empty($fid)) {
              $file = File::load($fid);
              $file->setPermanent();
              $file->save();
            }
          }
        }

      }
    }

    $config = $this->config('findeter_pqrsd.admin');

    $user = \Drupal\user\Entity\User::load($config->get('asign_user'));
    $newRequest->uid = $user->id(); 
    $newRequest->set('field_pqrsd_asignaciones', $user->getUsername().' | '.$user->id().' | '.date('j/m/Y H:i:s'));

    // channel and way to recipt the PQRSD
    $newRequest->set('field_pqrsd_canal_recepcion', 'web');
    $newRequest->set('field_pqrsd_forma_recepcion', 'electronico');

    // define date of answer
    $datesConfigure = defineDatesSemaphore($values);
    $newRequest->set('field_pqrsd_fecha_roja', $datesConfigure['red']);
    $newRequest->set('field_pqrsd_fecha_naranja', $datesConfigure['orange']);

    // set "# radicado"
    $newRequest->set('field_pqrsd_numero_radicado',generarNumeroRadicado());

    $newRequest->enforceIsNew();
    $newRequest->save();

    // send email
    if(isset($values['field_pqrsd_email']) && $values['field_pqrsd_email'] != ''){

      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'findeter_pqrsd';
      $key = 'registered_pqrsd';
      $to = $values['field_pqrsd_email'];

      $url = Url::fromRoute('findeter_pqrsd.status_pqrsd');
      $statusLink = Link::fromTextAndUrl('ingresar', $url);
      $statusLink = $statusLink->toRenderable();

      $mailBody[] = 'Reciba un cordial saludo de parte de Findeter';
      $mailBody[] = 'De antemano queremos agradecerle por haberse puesto en contacto con nosotros a traves del sistema de atención al usuario. Su opinión es muy importante para nosotros.';
      $mailBody[] = 'Le informamos que su solicitud ha sido registrada satisfactoriamente con el código de radicado:';
      $mailBody[] = '<div class="numero-radicado">'.$numeroRadicado.'</div>';
      $mailBody[] = 'Con este código podrá '.render($statusLink).' para consultar el estado de la misma y si es el caso ampliar o enviar información adicional';
      $mailBody[] = '';
      $mailBody[] = 'Cordialmente,';
      $mailBody[] = 'Vicepresidencia comercial - Servicio al cliente';
      $mailBody[] = 'Findeter';

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
    $values['pqrsd_numero_radicado'] = $numeroRadicado;
    $this->step->setValues($values);
  }

}