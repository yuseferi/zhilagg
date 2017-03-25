<?php

namespace Drupal\zhilagg;

use Drupal\zhilagg\Plugin\ZhilaggPluginManager;
use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Defines an importer of zhilagg items.
 */
class ItemsImporter implements ItemsImporterInterface {

  /**
   * The zhilagg fetcher manager.
   *
   * @var \Drupal\zhilagg\Plugin\ZhilaggPluginManager
   */
  protected $fetcherManager;

  /**
   * The zhilagg processor manager.
   *
   * @var \Drupal\zhilagg\Plugin\ZhilaggPluginManager
   */
  protected $processorManager;

  /**
   * The zhilagg parser manager.
   *
   * @var \Drupal\zhilagg\Plugin\ZhilaggPluginManager
   */
  protected $parserManager;

  /**
   * The zhilagg.settings config object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs an Importer object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\zhilagg\Plugin\ZhilaggPluginManager $fetcher_manager
   *   The zhilagg fetcher plugin manager.
   * @param \Drupal\zhilagg\Plugin\ZhilaggPluginManager $parser_manager
   *   The zhilagg parser plugin manager.
   * @param \Drupal\zhilagg\Plugin\ZhilaggPluginManager $processor_manager
   *   The zhilagg processor plugin manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ZhilaggPluginManager $fetcher_manager, ZhilaggPluginManager $parser_manager, ZhilaggPluginManager $processor_manager, LoggerInterface $logger) {
    $this->fetcherManager = $fetcher_manager;
    $this->processorManager = $processor_manager;
    $this->parserManager = $parser_manager;
    $this->config = $config_factory->get('zhilagg.settings');
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(FeedInterface $feed) {
    foreach ($this->processorManager->getDefinitions() as $id => $definition) {
      $this->processorManager->createInstance($id)->delete($feed);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function refresh(FeedInterface $feed) {
    // Store feed URL to track changes.
    $feed_url = $feed->getUrl();

    // Fetch the feed.
    try {
      $success = $this->fetcherManager->createInstance($this->config->get('fetcher'))->fetch($feed);
    }
    catch (PluginException $e) {
      $success = FALSE;
      watchdog_exception('zhilagg', $e);
    }

    // Store instances in an array so we dont have to instantiate new objects.
    $processor_instances = array();
    foreach ($this->config->get('processors') as $processor) {
      try {
        $processor_instances[$processor] = $this->processorManager->createInstance($processor);
      }
      catch (PluginException $e) {
        watchdog_exception('zhilagg', $e);
      }
    }

    // We store the hash of feed data in the database. When refreshing a
    // feed we compare stored hash and new hash calculated from downloaded
    // data. If both are equal we say that feed is not updated.
    $hash = hash('sha256', $feed->source_string);
    $has_new_content = $success && ($feed->getHash() != $hash);

    if ($has_new_content) {
      // Parse the feed.
      try {
        if ($this->parserManager->createInstance($this->config->get('parser'))->parse($feed)) {
          if (!$feed->getWebsiteUrl()) {
            $feed->setWebsiteUrl($feed->getUrl());
          }
          $feed->setHash($hash);
          // Update feed with parsed data.
          $feed->save();

          // Log if feed URL has changed.
          if ($feed->getUrl() != $feed_url) {
            $this->logger->notice('Updated URL for feed %title to %url.', array('%title' => $feed->label(), '%url' => $feed->getUrl()));
          }

          $this->logger->notice('There is new syndicated content from %site.', array('%site' => $feed->label()));

          // If there are items on the feed, let enabled processors process them.
          if (!empty($feed->items)) {
            foreach ($processor_instances as $instance) {
              $instance->process($feed);
            }
          }
        }
      }
      catch (PluginException $e) {
        watchdog_exception('zhilagg', $e);
      }
    }

    // Processing is done, call postProcess on enabled processors.
    foreach ($processor_instances as $instance) {
      $instance->postProcess($feed);
    }

    return $has_new_content;
  }

}
