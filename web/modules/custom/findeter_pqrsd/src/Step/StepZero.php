<?php

namespace Drupal\findeter_pqrsd\Step;

use Drupal\findeter_pqrsd\Step\StepsEnum;
use Drupal\findeter_pqrsd\Button\StepZeroNextButton;

use Drupal\findeter_pqrsd\Validator\ValidatorRequired;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\entity_browser\Element;
use Drupal\entity_browser\Element\EntityBrowserElement;
use Drupal\entity_browser\Plugin\Field\FieldWidget\EntityReferenceBrowserWidget;


/**
 * Class StepZero.
 *
 * @package Drupal\findeter_pqrsd\Step
 */
class StepZero extends BaseStep {

  // property to show messages
  private $messenger;

  /**
   * {@inheritdoc}
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
    $this->step = StepsEnum::STEP_ZERO;
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
  protected function setStep() {
    return StepsEnum::STEP_ZERO;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepZeroNextButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements($steps,$form,$form_state) {
    
    // Get data field definitions of content type
    $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'pqrsd');
    
    $formStep['title']['#markup'] = '
      <h2 class="text-center col-12">Registro de Formularios</h2><br><br>
      <p>"Gracias por ingresar al sistema atención al usuario del portal, su opinión es muy importante para nosotros. </p>
      <p>Lo invitamos a que consulte nuestras políticas y condiciones del servicio de atención. También puede contactar directamente a la entidad a través de nuestra línea telefónica gratuita de atención al cliente, Peticiones, Quejas, Reclamos y Denuncias 01-8000-116622.</p>
      <p>Si usted considera que su identidad debe mantenerse reservada al encontrarse en alguna de las circunstancias establecidas en el parágrafo del articulo 4  de la ley 1712/2014,deberá radicar  petición en este <a href="https://www.procuraduria.gov.co/portal/solicitud_informacion_identificacion_reservada.page">enlace</a>; de lo contrario lo invitamos a seleccionar el tipo de solicitud que desea presentar.</p>
      <p>Por favor tenga en cuenta las siguientes definiciones para establecer el tipo de solicitud a presentar y los <a href="https://www.findeter.gov.co/t%C3%A9rminos-de-respuesta">términos de respuesta</a> a tener en cuenta:</p>
    ';

    return $formStep;

  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [];
  }

}
