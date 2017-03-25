<?php

namespace Drupal\zhilagg;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Controller class for zhilagg's feeds.
 *
 * This extends the Drupal\Core\Entity\Sql\SqlContentEntityStorage class, adding
 * required special handling for feed entities.
 */
class FeedStorage extends SqlContentEntityStorage implements FeedStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function getFeedIdsToRefresh() {
    return $this->database->query('SELECT fid FROM {zhilagg_feed} WHERE queued = 0 AND checked + refresh < :time AND refresh <> :never', array(
      ':time' => REQUEST_TIME,
      ':never' => AGGREGATOR_CLEAR_NEVER,
    ))->fetchCol();
  }

}
