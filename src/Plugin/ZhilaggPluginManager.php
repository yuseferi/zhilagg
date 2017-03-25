<?php

namespace Drupal\zhilagg\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages zhilagg plugins.
 *
 * @see \Drupal\zhilagg\Annotation\ZhilaggParser
 * @see \Drupal\zhilagg\Annotation\ZhilaggFetcher
 * @see \Drupal\zhilagg\Annotation\ZhilaggProcessor
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginSettingsBase
 * @see \Drupal\zhilagg\Plugin\FetcherInterface
 * @see \Drupal\zhilagg\Plugin\ProcessorInterface
 * @see \Drupal\zhilagg\Plugin\ParserInterface
 * @see plugin_api
 */
class ZhilaggPluginManager extends DefaultPluginManager {

  /**
   * Constructs a ZhilaggPluginManager object.
   *
   * @param string $type
   *   The plugin type, for example fetcher.
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct($type, \Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    $type_annotations = array(
      'fetcher' => 'Drupal\zhilagg\Annotation\ZhilaggFetcher',
      'parser' => 'Drupal\zhilagg\Annotation\ZhilaggParser',
      'processor' => 'Drupal\zhilagg\Annotation\ZhilaggProcessor',
    );
    $plugin_interfaces = array(
      'fetcher' => 'Drupal\zhilagg\Plugin\FetcherInterface',
      'parser' => 'Drupal\zhilagg\Plugin\ParserInterface',
      'processor' => 'Drupal\zhilagg\Plugin\ProcessorInterface',
    );

    parent::__construct("Plugin/zhilagg/$type", $namespaces, $module_handler, $plugin_interfaces[$type], $type_annotations[$type]);
    $this->setCacheBackend($cache_backend, 'zhilagg_' . $type . '_plugins');
  }

}
