<?php

namespace Drupal\zhilagg\Plugin\QueueWorker;

use Drupal\zhilagg\FeedInterface;
use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Updates a feed's items.
 *
 * @QueueWorker(
 *   id = "zhilagg_feeds",
 *   title = @Translation("Zhilagg refresh"),
 *   cron = {"time" = 60}
 * )
 */
class ZhilaggRefresh extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    if ($data instanceof FeedInterface) {
      $data->refreshItems();
    }
  }

}
