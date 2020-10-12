<?php

namespace Drupal\findeter_user_form_request\Step;

use Drupal\findeter_user_form_request\Step\StepsEnum;
use Drupal\findeter_user_form_request\Button\StepTwoNextButton;
use Drupal\findeter_user_form_request\Button\StepTwoPreviousButton;

use Drupal\findeter_user_form_request\Validator\ValidatorRequired;
use Drupal\findeter_user_form_request\Validator\ValidatorEmail;
use Drupal\findeter_user_form_request\Validator\ValidatorNumber;
use Drupal\findeter_user_form_request\Validator\ValidatorLegalRequester;
use Drupal\findeter_user_form_request\Validator\ValidatorNaturalRequester;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StepTwo.
 *
 * @package Drupal\findeter_user_form_request\Step
 */
class StepTwo extends BaseStep {

  // property to show messages
  private $messenger;

  // to manage logs entry
  private $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(MessengerInterface $messenger,LoggerInterface $logger) {
    $this->messenger = $messenger;
    $this->logger = $logger;

    $this->step = StepsEnum::STEP_TWO;
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
  protected function setStep() {
    return StepsEnum::STEP_TWO;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepTwoPreviousButton(),
      new StepTwoNextButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($steps,$form,$form_state) {

    // Get the definitions
    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'user_request');

    //values previous step
    $values = $steps[1]->getValues();

    $form['step'] = array(
      '#markup' => '<ul class="steps-counter">
                      <li class="active">
                        <span class="number">1</span>
                        <span class="text">Radicar solicitud</span>
                      </li>
                      <li class="active">
                        <span class="number">2</span>
                        <span class="text">Información básica del solicitante</span>
                      </li>
                      <li>
                        <span class="number">3</span>
                        <span class="text">Información del producto</span>
                      </li>
                      <li>
                        <span class="number">4</span>
                        <span class="text">Canal de respuesta</span>
                      </li>
                    </ul>',
    );

    $form['content-fields'] = [
      '#type'       => 'container',
      '#attributes' => ['class' => ['row']],
      '#prefix'     => '<div class="container">'
    ];

    if($values['field_type_requester'] == 'juridica'){
      $form['content-fields']['title'] = array(
        '#markup' => '<h2 class="text-center col-12">Información básica: Persona jurídica</h2>',
      );

      $form['content-fields']['col1'] = [
        '#type'       => 'container',
        '#attributes' => ['class' => ['col']],
      ];

      $form['content-fields']['col1']['field_legal_nit'] = [
        '#type'       => 'textfield',
        '#title'      => '<span class"required">*</span>'.$definitions['field_legal_nit']->getLabel(),
        '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_legal_nit']->getLabel())]
      ];

      $form['content-fields']['col1']['field_legal_business_name'] = [
        '#type'       => 'textfield',
        '#title'      => '<span class"required">*</span>'.$definitions['field_legal_business_name']->getLabel(),
        '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_legal_business_name']->getLabel())]
      ];

      $form['content-fields']['col1']['field_legal_type_business'] = [
        '#type'         => 'select',
        '#title'        => '<span class"required">*</span>'.$definitions['field_legal_type_business']->getLabel(),
        '#options'      => $definitions['field_legal_type_business']->getSetting('allowed_values'),
        '#empty_option' => '-Seleccione una opción-',
      ];

    }else{
      $form['content-fields']['title'] = array(
        '#markup' => '<h2 class="text-center col-12">Información básica: Persona '.$values['field_type_requester'].'</h2>',
      );

      $form['content-fields']['col1'] = [
        '#type'       => 'container',
        '#attributes' => ['class' => ['col']],
      ];

      $form['content-fields']['col1']['field_person_number_id'] = [
        '#type'       => 'textfield',
        '#title'      => '<span class"required">*</span>'.$definitions['field_person_number_id']->getLabel(),
        '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_person_number_id']->getLabel())]
      ];

      $form['content-fields']['col1']['field_person_type_id'] = [
        '#type'    => 'select',
        '#title'   => $definitions['field_person_type_id']->getLabel(),
        '#options' => $definitions['field_person_type_id']->getSetting('allowed_values')
      ];

    }

    $form['content-fields']['col1']['field_person_first_name'] = [
      '#type'       => 'textfield',
      '#title'      => '<span class"required">*</span>'.$definitions['field_person_first_name']->getLabel(),
      '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_person_first_name']->getLabel())]
    ];

    $form['content-fields']['col1']['field_person_second_name'] = [
      '#type'       => 'textfield',
      '#title'      => $definitions['field_person_second_name']->getLabel(),
      '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_person_second_name']->getLabel())]
    ];

    $form['content-fields']['col1']['field_person_first_lastname'] = [
      '#type'       => 'textfield',
      '#title'      => '<span class"required">*</span>'.$definitions['field_person_first_lastname']->getLabel(),
      '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_person_first_lastname']->getLabel())]
    ];

    $form['content-fields']['col1']['field_person_second_lastname'] = [
      '#type'       => 'textfield',
      '#title'      => $definitions['field_person_second_lastname']->getLabel(),
      '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_person_second_lastname']->getLabel())]
    ];

    if($values['field_type_requester'] == 'juridica'){
      $form['content-fields']['col1']['field_person_first_name']['#title'] .= ' del representante legal';
      $form['content-fields']['col1']['field_person_second_name']['#title'] .= ' del representante legal';
      $form['content-fields']['col1']['field_person_first_lastname']['#title'] .= ' del representante legal';
      $form['content-fields']['col1']['field_person_second_lastname']['#title'] .= ' del representante legal';
    }

    $form['content-fields']['col2'] = [
      '#type'       => 'container',
      '#attributes' => ['class' => ['col']],
    ];

    $form['content-fields']['col2']['field_person_address'] = [
      '#type'       => 'textfield',
      '#title'      => '<span class"required">*</span>'.$definitions['field_person_address']->getLabel(),
      '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_person_address']->getLabel())]
    ];


    $deparmentOptions = $this->getTaxonomyTermsForm(0);

    $form['content-fields']['col2']['field_person_deparment'] = [
      '#type'    => 'select',
      '#title'   => '<span class"required">*</span>'.$definitions['field_person_deparment']->getLabel(),
      '#options' => $deparmentOptions,
      '#ajax'    => [
        'callback'  => [$this, 'callBackDeparment'], 
        'event'     => 'change',
        'progress'  => [
          'message' => 'Recupersando municipios...',
        ],
      ],
      '#empty_option'   => '-Seleccione una opción-',
    ];

    $departmentValue = false;
    if($form_state->getValue('field_person_deparment') != ''){
      $departmentValue = $form_state->getValue('field_person_deparment');
    }elseif(isset($steps[2]->values['field_person_deparment'])){
      $departmentValue = $steps[2]->values['field_person_deparment'];
    }

    $form['content-fields']['col2']['field_person_municipality'] = [
      '#type'      => 'select',
      '#title'     => '<span class"required">*</span>'.$definitions['field_person_municipality']->getLabel(),
      '#prefix'    => '<div id="output-municipalities">',
      '#suffix'    => '</div>',
      '#empty_option' => '-Seleccione una opción-',
    ];

    if ($departmentValue) {
      $form['content-fields']['col2']['field_person_municipality']['#options'] = $this->getTaxonomyTermsForm($departmentValue);
    }

    $form['content-fields']['col2']['field_person_phone_contact'] = [
      '#type'       => 'textfield',
      '#title'      => '<span class"required">*</span>'.$definitions['field_person_phone_contact']->getLabel(),
      '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_person_phone_contact']->getLabel())]
    ];

    $form['content-fields']['col2']['field_person_fax_contact'] = [
      '#type'       => 'textfield',
      '#title'      => $definitions['field_person_fax_contact']->getLabel(),
      '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_person_fax_contact']->getLabel())]
    ];
    
    $form['content-fields']['col2']['field_person_email'] = [
      '#type'       => 'email',
      '#title'      => '<span class"required">*</span>'.$definitions['field_person_email']->getLabel(),
      '#attributes' => ['placeholder'=>'Diligencie su '.strtolower($definitions['field_person_email']->getLabel())]
    ];

    //Populate values
    if(isset($steps[2])){
      foreach($steps[2]->values as $field=>$value){
        if(isset($form[$field])){
          $form[$field]['#default_value'] = $value;
        }
      }
    }
    
    return $form;
  }


  /**
   * Callback deparment select ajax event
   */
  public function callBackDeparment(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();

    $parent_tid = $form_state->getValue('field_person_deparment'); // the parent term id
    $municipalityOptions = $this->getTaxonomyTermsForm($parent_tid);
  
    $elem = $form['wrapper']['content-fields']['col2']['field_person_municipality'];
    
    $elem['#options'] = $municipalityOptions;
    $renderer = \Drupal::service('renderer');
    $renderedField = $renderer->render($elem);

    $response = new AjaxResponse();
  
    $response->addCommand(new ReplaceCommand('#output-municipalities', $renderedField));
    
    return $response;
  }


  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'field_legal_nit',
      'field_legal_business_name',
      'field_legal_type_business',
      'field_person_number_id',
      'field_person_type_id',
      'field_person_first_name',
      'field_person_second_name',
      'field_person_first_lastname',
      'field_person_second_lastname',
      'field_person_address',
      'field_person_deparment',
      'field_person_municipality',
      'field_person_phone_contact',
      'field_person_fax_contact',
      'field_person_email',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'field_legal_nit' => [
        new ValidatorLegalRequester("Nit es requerido"),
      ],
      'field_legal_business_name' => [
        new ValidatorLegalRequester("Razón social es requerido"),
      ],
      'field_legal_type_business' => [
        new ValidatorLegalRequester("Tipo de empresa es requerido"),
      ],
      'field_person_number_id' => [
        new ValidatorNaturalRequester("Número identificación es requerido"),
        new ValidatorNumber("Número identificación solo permite números"),
      ],
      'field_person_type_id' => [
        new ValidatorNaturalRequester("Tipo documento ID es requerido"),
      ],
      'field_person_first_name' => [
        new ValidatorRequired("Primer nombre es requerido"),
      ],
      'field_person_first_lastname' => [
        new ValidatorRequired("Primer apellido es requerido"),
      ],
      'field_person_address' => [
        new ValidatorRequired("Dirección correspondencia es requerido"),
      ],
      'field_person_deparment' => [
        new ValidatorRequired("Departamento es requerido"),
      ],
      'field_person_municipality' => [
        new ValidatorRequired("Municipio es requerido"),
      ],
      'field_person_fax_contact' => [
        new ValidatorNumber("Fax solo permite números"),
      ],
      'field_person_phone_contact' => [
        new ValidatorRequired("Teléfono de contacto es requerido"),
        new ValidatorNumber("Teléfono de contacto solo permite números"),
      ],
      'field_person_email' => [
        new ValidatorRequired("Correo electrónico es requerido "),
        new ValidatorEmail("Correo electrónico inválido"),
      ],
    ];
  }

  /**
   * Create an array with the taxonomy terms
   */
  function getTaxonomyTermsForm($tid){
    $child_tids = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('colombia_deparments', $tid, 1, false);

    $options = array();
    foreach ($child_tids as $term) {
      $options[$term->tid] = $term->name;
    }
    return $options;
  }

}
