<?php

namespace Drupal\zhilagg\Plugin;

use Drupal\zhilagg\FeedInterface;

/**
 * Defines an interface for zhilagg fetcher implementations.
 *
 * A fetcher downloads feed data to a Drupal site. The fetcher is called at the
 * first of the three aggregation stages: first, data is downloaded by the
 * active fetcher; second, it is converted to a common format by the active
 * parser; and finally, it is passed to all active processors, which manipulate
 * or store the data.
 *
 * @see \Drupal\zhilagg\Annotation\ZhilaggFetcher
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginSettingsBase
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginManager
 * @see plugin_api
 */
interface FetcherInterface {

  /**
   * Downloads feed data.
   *
   * @param \Drupal\zhilagg\FeedInterface $feed
   *   A feed object representing the resource to be downloaded.
   *   $feed->getUrl() contains the link to the feed.
   *   Download the data at the URL and expose it
   *   to other modules by attaching it to $feed->source_string.
   *
   * @return bool
   *   TRUE if fetching was successful, FALSE otherwise.
   */
  public function fetch(FeedInterface $feed);

}
