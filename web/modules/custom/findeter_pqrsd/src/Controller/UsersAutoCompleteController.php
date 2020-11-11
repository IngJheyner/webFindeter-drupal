<?php

namespace Drupal\findeter_pqrsd\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;

/**
 * Defines a route controller for watches autocomplete form elements.
 */
class UsersAutoCompleteController extends ControllerBase {

  /**
   * The node storage.
   *
   * @var \Drupal\node\NodeStorage
   */
  protected $nodeStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->nodeStroage = $entity_type_manager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Handler for autocomplete request.
   */
  public function handleAutocomplete(Request $request) {
    
    $config = $this->config('findeter_pqrsd.admin');
    
    $results = [];
    $input = $request->query->get('q');

    // Get the typed string from the URL, if it exists.
    if (!$input) {
      return new JsonResponse($results);
    }

    $input = Xss::filter($input);

    $userStorage = \Drupal::entityTypeManager()->getStorage('user');
    $rolesAllowed = explode(',',$config->get('roles'));

    $query = $userStorage->getQuery();
    $uids = $query
      ->condition('status', '1')
      ->condition('name', $input, 'CONTAINS')
      ->condition('roles', $rolesAllowed,'IN')
      ->execute();
      
    $users = $uids ? $userStorage->loadMultiple($uids) : [];

    foreach ($users as $user) {
  
      $label = [
        $user->getUsername(),
          '<small>(' . $user->id() . ')</small>'
      ];
  
      $results[] = [
        'value' => EntityAutocomplete::getEntityLabels([$user]),
        'label' => implode(' ', $label),
      ];
    }

    return new JsonResponse($results);
    
  }


  /**
   * Handler for autocomplete request.
   */
  public function handleList() {
    
    $config = $this->config('findeter_pqrsd.admin');
    
    $results = [];

    $userStorage = \Drupal::entityTypeManager()->getStorage('user');
    $rolesAllowed = explode(',',$config->get('roles'));

    $query = $userStorage->getQuery();
    $uids = $query
      ->condition('status', '1')
      ->condition('roles', $rolesAllowed,'IN')
      ->execute();
      
    $users = $uids ? $userStorage->loadMultiple($uids) : [];

    $html = '<table>';
    foreach ($users as $user) {
      $mail = $user->getEmail();
      $username = $user->getUsername();
      $rel = $username.'('.$user->id().')';
      $name = '<a class="user-asign-link" href="#" rel="'.$rel.'">'.$username.'</a>';

      $html .= '<tr><td>'.$name.'</td>'.'<td>'.$mail.'</td></tr>';
    }

    $output['message'] = array(
      '#markup' => $html,
    );

    return $output;
    
  }

}