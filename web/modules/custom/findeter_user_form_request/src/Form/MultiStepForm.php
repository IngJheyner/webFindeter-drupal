<?php

namespace Drupal\findeter_user_form_request\Form;

use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\findeter_user_form_request\Manager\StepManager;
use Drupal\findeter_user_form_request\Step\StepsEnum;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\views\Ajax\ScrollTopCommand;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;

/**
 * Provides multi step ajax example form.
 *
 * @package Drupal\findeter_user_form_request\Form
 */
class MultiStepForm extends FormBase {
  use StringTranslationTrait;
  
    /**
   * Step Id.
   *
   * @var \Drupal\findeter_user_form_request\Step\StepsEnum
   */
  protected $stepId;

    /**
   * Multi steps of the form.
   *
   * @var \Drupal\findeter_user_form_request\Step\StepInterface
   */
  protected $step;

  /**
   * Step manager instance.
   *
   * @var \Drupal\findeter_user_form_request\Manager\StepManager
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
  public function __construct(MessengerInterface $messenger,LoggerInterface $logger) {
    $this->stepId = StepsEnum::STEP_ZERO;
    // StepManager class needs those two arguments
    $this->stepManager = new StepManager($messenger,$logger);
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('logger.factory')->get('multiStep')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_request_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get step from step manager.
    $this->step = $this->stepManager->getStep($this->stepId);

    $form['wrapper-messages'] = [
      '#type'       => 'container',
      '#attributes' => [
        'id' => 'messages-wrapper',
      ],
    ];

    $form['wrapper'] = [
      '#type'       => 'container',
      '#attributes' => [
        'id' => 'form-wrapper'
      ],
    ];

    $textSteps[0] = 'Inicio';
    $textSteps[1] = 'Radicar solicitud';
    $textSteps[2] = 'Información básica del solicitante';
    $textSteps[3] = 'Información del producto';
    $textSteps[4] = 'Canal de respuesta';

    $htmlStep = '';
    foreach($textSteps as $nStep=>$textStep){
      $class = '';
      
      if($nStep <= $this->stepId){
        $class = 'class="active"';
      }

      $htmlStep .= '
        <li '.$class.'>
          <span class="number">'.($nStep+1).'</span>
          <span class="text">'.$textStep.'</span>
        </li>';
    }

    // don't show finish step (6th step)
    if($this->stepId < 6){
      $form['wrapper']['wrapper-step'] = array(
        '#markup' => '<ul class="steps-counter">'.$htmlStep.'</ul>',
      );
    }
    
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
    // Begin of div in each step config
    //$form['wrapper']['actions']['#suffix'] = '</div>';
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

    $form_state->setRebuild();
    $form['#attached']['library'][] = 'findeter_user_form_request/global-scripts';

    // populate fields with stored values
    if($this->step->getValues() != ''){
      foreach($this->step->getValues() as $field=>$value){
        if(isset($form['wrapper']['content-fields'][$field])){
          $form['wrapper']['content-fields'][$field]['#default_value'] = $value;
        }
      }
    }

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
    //$response->addCommand(new HtmlCommand('#form-wrapper',$form['wrapper']));

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
      $values['field_type_form'] = $triggering_element['#value'];
    } 

    $this->step->setValues($values);
    // Add step to manager.
    $this->stepManager->addStep($this->step);
    
    $this->stepId = $triggering_element['#goto_step'];

    // for anonimo request, jump step 2
    $allSteps = $this->stepManager->getAllSteps();    
    if(isset($allSteps[1])){
      $valuesStep1 = $allSteps[1]->getValues();
      if($valuesStep1['field_type_requester'] == 'anonimo'){
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
    $newRequest = Node::create(['type' => 'user_request']);
    $newRequest->set('title', 'User request - '.date('U'));

    //to retrive all values at one single array
    $values = []; 
    foreach($steps as $step){
      foreach($step->getValues() as $field=>$value){

        //adding all values in the same array
        $values += $step->getValues();

        if($field == 'field_request_marketing'){
          if($value){
            $value = 'autorizacion_marketing';
          }else{
            $value = '';
          } 
        }

        if($field == 'field_authorization'){
          $value = 'autorizacion_findeter';
        }

        if($value != ''){
          $newRequest->set($field, $value);
        }

        // store all files
        if($field == 'field_request_files'){
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

    $config = $this->config('findeter.admin');

    $user = \Drupal\user\Entity\User::load($config->get('asign_user'));
    $newRequest->uid = $user->id(); 
    $newRequest->set('field_request_designatations', $user->getUsername().' | '.$user->id().' | '.date('j/m/Y H:i:s'));

    // channel and way to recipt the PQRSD
    $newRequest->set('field_request_receiving_channel', 'web');
    $newRequest->set('field_request_reception_form', 'electronico');

    // define date of answer
    $datesConfigure = defineDatesSemaphore($values);
    $newRequest->set('field_request_date_red', $datesConfigure['red']);
    $newRequest->set('field_request_date_orange', $datesConfigure['orange']);

    $newRequest->enforceIsNew();
    $newRequest->save();


    // store the nid value of new node created
    $values['new_nid'] = $newRequest->id();
    $this->step->setValues($values);
  }

}
