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
use Drupal\Component\Utility\Xss;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\views\Views;

use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

/**
 * Provides multi step ajax example form.
 *
 * @package Drupal\findeter_pqrsd\Form
 */
class StatusPQRSD extends FormBase {
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
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'status_pqrsd';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // where all messages will printed
    $form['wrapper-messages'] = [
      '#type'       => 'container',
      '#attributes' => [
        'id' => 'messages-wrapper',
      ],
    ];


    // contain all form and listen ajax event to rebuild the entry form
    $form['wrapper-status'] = [
      '#type'       => 'container',
      '#attributes' => [
        'class'=>['row']
      ],
    ];

    $form['wrapper-status']['form-radicado'] = [
      '#type'       => 'container',
      '#attributes' => [
        'class'=>['col-6']
      ],
    ];

    $form['wrapper-status']['form-radicado']['radicado_number'] = [
      '#type'       => 'textfield',
      '#title'      => '# Radicado',
      '#required'=>true,
      '#attributes' => ['placeholder'=>'Diligencie el número de su radicado']
    ];

    $form['wrapper-status']['form-radicado']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Consultar',
      '#ajax' => [
        'callback' => '::consultStatus',
        //'wrapper'  => 'form-wrapper',
        'effect'   => 'fade',
      ],
    ];

    $form['wrapper-status']['status-radicado'] = [
      '#type'       => 'container',
      '#attributes' => [
        'class'=>['col-6'],
        'id' => 'edit-status-radicado'
      ],
    ];

    $form['wrapper-status']['status-radicado']['radicado_text'] = [

      '#markup' => '<p>Ingrese el número de su radicado en el formulario de la izquierda. Este número se le presenta al finalizar el registro de un radicado, como por ejemplo:</p>
        <div class="number-example">5692TD</div>
        <p>También puede revisar el email enviado a su correo electrónico.</p>
      '

    ];

    //$form['#attached']['library'][] = 'findeter_pqrsd/global-scripts';

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
  public function consultStatus(array &$form, FormStateInterface $form_state) {

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

    $responseHtml = [];


    $numberRadicado = $form_state->getValue('radicado_number');

    if($numberRadicado!='') {
      $query = \Drupal::entityQuery('node')
        ->condition('type', 'pqrsd')
        ->condition('field_pqrsd_numero_radicado', $numberRadicado);
      $nid = array_pop($query->execute());


      if($node = \Drupal\node\Entity\Node::load($nid)){

        if($node->get('field_pqrsd_primer_nombre')->value != ''){
          $radicatorName = $node->get('field_pqrsd_primer_nombre')->value;
          $radicatorName .= $node->get('field_pqrsd_segundo_nombre')->value;
          $radicatorName .= $node->get('field_pqrsd_primer_apellido')->value;
          $radicatorName .= $node->get('field_pqrsd_segundo_apellido')->value;
        }else{
          $radicatorName = 'Anónimo';
        }

        $responseHtml[] = '<b>Nombre radicador: </b> '.$radicatorName;
        $responseHtml[] = '<b>Fecha registro: </b> '.date('d/m/Y H:i',$node->get('created')->value);
        $responseHtml[] = '<b>Fecha máxima de respuesta: </b> '.date('d/m/Y',strtotime($node->get('field_pqrsd_fecha_roja')->value));
        $responseHtml[] = '<b>Asunto: </b> '.$node->get('field_pqrsd_descripcion')->value;

        $responseHtml[] = '<div class="status-answer"><b>Estado de su radicatoria:</b>';

        if($node->get('field_pqrsd_respuesta_archivos')->getValue() != ''){

          $filesList = [];
          foreach ($node->get('field_pqrsd_respuesta_archivos')->getValue() as $fileItem){
            $file_id = $fileItem['target_id'];
            $file = \Drupal\file\Entity\File::load($file_id);
            $uri = $file->uri->value;
            $filesList[] = file_create_url($uri);
          }

          $responseHtml[] = 'Respondida';
          $responseHtml[] = '<b>Respuesta: </b> '.$node->get('field_pqrsd_respuesta')->value;
          $responseHtml[] = '<ul>';
          foreach($filesList as $fil){
            $responseHtml[] = '<li><b><a href="'.$fil.'">Archivo de la respuesta</a></b></li>';
          }
          $responseHtml[] = '</ul>';

          $responseHtml[] = '</div>';
        }else{
          $responseHtml[] = 'Pendiente de respuesta</div>';
        }

      }else{
        $responseHtml[] = '<p>No hemos encontrado un radicado con el número que indica.</p>';
      }

    }

    $responseText = [
      '#markup' => implode('<br>',$responseHtml)
    ];

    // Update Form.
    $response->addCommand(new HtmlCommand('#edit-status-radicado',$responseText));

    return $response;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
  }



}
