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
      'image' => is_null($image) ? '' : file_create_url($image->getFileUri()),
      'subsectors' => $subsectors,
    ];

    // Retornamos las variables en este caso datos de tipo json.
    return new JsonResponse([
      'status' => 'success',
      'sectors' => $data,
    ]);
  }

  /**
   * Se trae los datos de ciiu redescuento segun sea su codigo.
   *
   * @param int $code
   *   - Parametro $code con argumento de identificacion del nodo por medio del codigo(title).
   */
  public function searchCodeCiiu($code) {

    // Query de tipo nodo.
    $query = $this->entityTypeManager->getStorage('node')->getQuery();

    $nodeStorage = $this->entityTypeManager->getStorage('node');

    // Se carga el nodo con el query consultado.
    $nid = $query->condition('type', 'ciiu_rediscount')
      ->condition('title', $code)
      ->execute();

    $data = [];
    if (count($nid) > 0) {
      // Cargamos el node.
      $nid = array_values($nid);
      $node = $nodeStorage->load($nid[0]);

      // Nombre de la actividad.
      $data['activity'] = $node->get('field_activity_ciiu')->value;

      // Referenciamos los secetores.
      $entityReferences = $node->get('field_sectors_ciiu')->referencedEntities();

      // Se itera para obtener los nombres de los sectores.
      foreach ($entityReferences as $key => $value) {
        $data['sectors'][] = [
          'title' => $value->getTitle(),
        ];
      }
      // Validacion de ciiu si es o no financiable.
      $data['bankables'] = $node->get('field_bankables_ciiu')->value;
    }

    // Retornamos las variables en este caso datos de tipo json.
    return new JsonResponse([
      'status' => 'success',
      'ciiu' => $data,
    ]);

  }

}
