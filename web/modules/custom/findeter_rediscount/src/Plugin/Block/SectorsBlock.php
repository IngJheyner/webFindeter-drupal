<?php

namespace Drupal\findeter_rediscount\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides an sectors block.
 *
 * @Block(
 *   id = "findeter_rediscount_sectors",
 *   admin_label = @Translation("Sectores"),
 *   category = @Translation("findeter_rediscount")
 * )
 */

class SectorsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal Core.
   *
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $node_storage = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'sectors_rediscount']);
    $file_storage = $this->entityTypeManager->getStorage('file');

    $data = [];
    $subsectors = [];
    $count = count($node_storage);

    foreach ($node_storage as $key => $node) {

      $icon = $file_storage->load($node->get('field_icon_sector')->target_id);
      $iconSecond = $file_storage->load($node->get('field_icon_second_sector')->target_id);
      $image = $file_storage->load($node->get('field_image_sector')->target_id);

      $subsectorsItems = $node->get('field_subsectors_rediscount')->referencedEntities();

      foreach ($subsectorsItems as $keyItem => $paragraph) {

        $subsectors[$keyItem] = [
          'title' => $paragraph->get('field_tittle_product_prgms')->value,
          'description' => $paragraph->get('field_description_prgms')->value,
        ];

      }

      $data[$key] = [
        'nid' => $node->id(),
        'icon' => $icon->getFileUri(),
        'icon_secon' => $iconSecond->getFileUri(),
        'title' => $node->getTitle(),
        'description' => $node->get('field_description_sector')->value,
        'image' => $image->getFileUri(),
        'subsectors' => $subsectors,
      ];

    }
    // dump($data); die();

    return [
      // Your theme hook name.
      '#theme' => 'findeter_rediscount_sectors',
      // The variables to pass to the theme template file.
      '#data' => $data,
      '#count' => $count,
    ];

  }

}
