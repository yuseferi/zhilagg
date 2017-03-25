<?php

namespace Drupal\zhilagg\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Plugin annotation object for zhilagg processor plugins.
 *
 * Plugin Namespace: Plugin\zhilagg\processor
 *
 * For a working example, see \Drupal\zhilagg\Plugin\zhilagg\processor\DefaultProcessor
 *
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginManager
 * @see \Drupal\zhilagg\Plugin\ProcessorInterface
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginSettingsBase
 * @see plugin_api
 *
 * @Annotation
 */
class ZhilaggProcessor extends Plugin {

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
