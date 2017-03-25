<?php

namespace Drupal\zhilagg\Plugin;

use Drupal\zhilagg\FeedInterface;

/**
 * Defines an interface for zhilagg processor implementations.
 *
 * A processor acts on parsed feed data. Active processors are called at the
 * third and last of the aggregation stages: first, data is downloaded by the
 * active fetcher; second, it is converted to a common format by the active
 * parser; and finally, it is passed to all active processors that manipulate or
 * store the data.
 *
 * @see \Drupal\zhilagg\Annotation\ZhilaggProcessor
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginSettingsBase
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginManager
 * @see plugin_api
 */
interface ProcessorInterface {

  /**
   * Processes feed data.
   *
   * @param \Drupal\zhilagg\FeedInterface $feed
   *   A feed object representing the resource to be processed.
   *   $feed->items contains an array of feed items downloaded and parsed at the
   *   parsing stage. See \Drupal\zhilagg\Plugin\FetcherInterface::parse()
   *   for the basic format of a single item in the $feed->items array.
   *   For the exact format refer to the particular parser in use.
   */
  public function process(FeedInterface $feed);

  /**
   * Refreshes feed information.
   *
   * Called after the processing of the feed is completed by all selected
   * processors.
   *
   * @param \Drupal\zhilagg\FeedInterface $feed
   *   Object describing feed.
   *
   * @see zhilagg_refresh()
   */
  public function postProcess(FeedInterface $feed);

  /**
   * Deletes stored feed data.
   *
   * Called by zhilagg if either a feed is deleted or a user clicks on
   * "delete items".
   *
   * @param \Drupal\zhilagg\FeedInterface $feed
   *   The $feed object whose items are being deleted.
   */
  public function delete(FeedInterface $feed);

}
