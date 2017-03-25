<?php

namespace Drupal\zhilagg\Plugin\views\row;

use Drupal\views\Plugin\views\row\RssPluginBase;

/**
 * Defines a row plugin which loads an zhilagg item and renders as RSS.
 *
 * @ViewsRow(
 *   id = "zhilagg_rss",
 *   theme = "views_view_row_rss",
 *   title = @Translation("Zhilagg item"),
 *   help = @Translation("Display the zhilagg item using the data from the original source."),
 *   base = {"zhilagg_item"},
 *   display_types = {"feed"}
 * )
 */
class Rss extends RssPluginBase {

  /**
   * The table the zhilagg item is using for storage.
   *
   * @var string
   */
  public $base_table = 'zhilagg_item';

  /**
   * The actual field which is used to identify a zhilagg item.
   *
   * @var string
   */
  public $base_field = 'iid';

  /**
   * {@inheritdoc}
   */
  protected $entityTypeId = 'zhilagg_item';

  /**
   * {@inheritdoc}
   */
  public function render($row) {
    $entity = $row->_entity;

    $item = new \stdClass();
    foreach ($entity as $name => $field) {
      $item->{$name} = $field->value;
    }

    $item->elements = array(
      array(
        'key' => 'pubDate',
        // views_view_row_rss takes care about the escaping.
        'value' => gmdate('r', $entity->timestamp->value),
      ),
      array(
        'key' => 'dc:creator',
        // views_view_row_rss takes care about the escaping.
        'value' => $entity->author->value,
      ),
      array(
        'key' => 'guid',
        // views_view_row_rss takes care about the escaping.
        'value' => $entity->guid->value,
        'attributes' => array('isPermaLink' => 'false'),
      ),
    );

    $build = array(
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#options' => $this->options,
      '#row' => $item,
    );
    return $build;
  }

}
