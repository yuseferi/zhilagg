<?php

namespace Drupal\zhilagg;

use Drupal\Core\Entity\EntityViewBuilder;

/**
 * View builder handler for zhilagg feed items.
 */
class ItemViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildComponents(array &$build, array $entities, array $displays, $view_mode) {
    parent::buildComponents($build, $entities, $displays, $view_mode);

    foreach ($entities as $id => $entity) {
      $bundle = $entity->bundle();
      $display = $displays[$bundle];

      if ($display->getComponent('description')) {
        $build[$id]['description'] = array(
          '#markup' => $entity->getDescription(),
          '#allowed_tags' => _zhilagg_allowed_tags(),
          '#prefix' => '<div class="item-description">',
          '#suffix' => '</div>',
        );
      }
    }
  }

}
