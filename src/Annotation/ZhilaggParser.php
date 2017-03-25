<?php

namespace Drupal\zhilagg\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Plugin annotation object for zhilagg parser plugins.
 *
 * Plugin Namespace: Plugin\zhilagg\parser
 *
 * For a working example, see \Drupal\zhilagg\Plugin\zhilagg\parser\DefaultParser
 *
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginManager
 * @see \Drupal\zhilagg\Plugin\ParserInterface
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginSettingsBase
 * @see plugin_api
 *
 * @Annotation
 */
class ZhilaggParser extends Plugin {

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
