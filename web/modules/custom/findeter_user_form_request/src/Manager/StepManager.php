<?php

namespace Drupal\findeter_user_form_request\Manager;

use Drupal\findeter_user_form_request\Step\StepInterface;
use Drupal\findeter_user_form_request\Step\StepsEnum;

use Drupal\Core\Messenger\MessengerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StepManager.
 *
 * @package Drupal\findeter_user_form_request\Manager
 */
class StepManager {

  // property to show messages
  private $messenger;

  // to manage logs entry
  private $logger;

  /**
   * Multi steps of the form.
   *
   * @var \Drupal\findeter_user_form_request\Step\StepInterface
   */
  protected $steps;


  /**
   * {@inheritdoc}
   */
  public function __construct(MessengerInterface $messenger,LoggerInterface $logger) {
    $this->messenger = $messenger;
    $this->logger = $logger;
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
   * Add a step to the steps property.
   *
   * @param \Drupal\findeter_user_form_request\Step\StepInterface $step
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
   * @return \Drupal\findeter_user_form_request\Step\StepInterface
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
      $step = new $class($this->messenger,$this->logger,$this);
    }
    
    return $step;
  }

  /**
   * Get all steps.
   *
   * @return \Drupal\findeter_user_form_request\Step\StepInterface
   *   Steps.
   */
  public function getAllSteps() {
    return $this->steps;
  }

}
