<?php

namespace Drupal\zhilagg\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Plugin annotation object for zhilagg fetcher plugins.
 *
 * Plugin Namespace: Plugin\zhilagg\fetcher
 *
 * For a working example, see \Drupal\zhilagg\Plugin\zhilagg\fetcher\DefaultFetcher
 *
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginManager
 * @see \Drupal\zhilagg\Plugin\FetcherInterface
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginSettingsBase
 * @see plugin_api
 *
 * @Annotation
 */
class ZhilaggFetcher extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The title of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;

  /**
   * The description of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

}
