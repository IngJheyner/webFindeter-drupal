<?php

namespace Drupal\findeter_pqrsd\Manager;

use Drupal\findeter_pqrsd\Step\StepInterface;
use Drupal\findeter_pqrsd\Step\StepsEnum;

use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StepManager.
 *
 * @package Drupal\findeter_pqrsd\Manager
 */
class StepManager {

  // property to show messages
  private $messenger;

  /**
   * Multi steps of the form.
   *
   * @var \Drupal\findeter_pqrsd\Step\StepInterface
   */
  protected $steps;


  /**
   * {@inheritdoc}
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      //$container->get('logger.factory')->get('multiStep')
    );
  }

  /**
   * Add a step to the steps property.
   *
   * @param \Drupal\findeter_pqrsd\Step\StepInterface $step
   *   Step of the form.
   */
  public function addStep(StepInterface $step) {
    $this->steps[$step->getStep()] = $step;
  }

  /**
   * Fetches step from steps property, If it doesn't exist, create step object.
   *
   * @param int $step_id
   *   Step ID.
   *
   * @return \Drupal\findeter_pqrsd\Step\StepInterface
   *   Return step object.
   */
  public function getStep($step_id) {
    if (isset($this->steps[$step_id])) {
      // If step was already initialized, use that step.
      // Chance is there are values stored on that step.
      $step = $this->steps[$step_id];
    }
    else {
      // Get class.
      $class = StepsEnum::map($step_id);
      // Init step.
      $step = new $class($this->messenger,$this);
    }
    
    return $step;
  }

  /**
   * Get all steps.
   *
   * @return \Drupal\findeter_pqrsd\Step\StepInterface
   *   Steps.
   */
  public function getAllSteps() {
    return $this->steps;
  }

}
