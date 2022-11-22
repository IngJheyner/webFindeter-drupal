<?php

namespace Drupal\findeter_rediscount\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for findeter_rediscount routes.
 */
class FindeterRediscountController extends ControllerBase {

  /**
   * Drupal Core.
   *
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Se trae los datos de sectores segun sea su nid.
   *
   * @param int $nid
   *   - Parametro $nid con argumento de identificacion del nodo.
   */
  public function sectorsInfo($nid) {

    // Sotorage de tipo nodo y file.
    $nodeStorage = $this->entityTypeManager->getStorage('node');
    $file_storage = $this->entityTypeManager->getStorage('file');

    // Se carga los arhivos y nodos segun sea su nid.
    $sectors = $nodeStorage->load($nid);
    $image = $file_storage->load($sectors->get('field_image_sector')->target_id);

    $subsectors = [];
    $subsectorsItems = $sectors->get('field_subsectors_rediscount')->referencedEntities();

    foreach ($subsectorsItems as $keyItem => $paragraph) {

      $subsectors[$keyItem] = [
        'title' => $paragraph->get('field_tittle_product_prgms')->value,
        'description' => $paragraph->get('field_description_prgms')->value,
      ];

    }

    // Se agrega una variable de tipo arreglo donde recoje los datos del nodo sectores.
    $data = [
      'title' => $sectors->getTitle(),
      'description' => $sectors->get('field_description_sector')->value,
      'image' => file_create_url($image->getFileUri()),
      'subsectors' => $subsectors,
    ];

    // Retornamos las variables en este caso datos de tipo json.
    return new JsonResponse([
      'status' => 'success',
      'sectors' => $data,
    ]);
  }

}
