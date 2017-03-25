<?php

namespace Drupal\zhilagg;

use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Defines an interface for zhilagg feed entity storage classes.
 */
interface FeedStorageInterface extends ContentEntityStorageInterface {

  /**
   * Returns the fids of feeds that need to be refreshed.
   *
   * @return array
   *   A list of feed ids to be refreshed.
   */
  public function getFeedIdsToRefresh();

}
