<?php

namespace Drupal\zhilagg\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'zhilagg_xss' formatter.
 *
 * @FieldFormatter(
 *   id = "zhilagg_xss",
 *   label = @Translation("Zhilagg XSS"),
 *   description = @Translation("Filter output for zhilagg items"),
 *   field_types = {
 *     "string",
 *     "string_long",
 *   }
 * )
 */
class ZhilaggXSSFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'markup',
        '#markup' => $item->value,
        '#allowed_tags' => _zhilagg_allowed_tags(),
      ];
    }
    return $elements;
  }

}
