<?php

/**
 * @file
 * Preprocessors and theme functions of Zhilagg module.
 */

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Render\Element;

/**
 * Prepares variables for zhilagg item templates.
 *
 * Default template: zhilagg-item.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An array of elements to display in view mode.
 */
function template_preprocess_zhilagg_item(&$variables) {
  $item = $variables['elements']['#zhilagg_item'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }

  $variables['url'] = UrlHelper::stripDangerousProtocols($item->getLink());
  $variables['title'] = $item->label();
}

/**
 * Prepares variables for zhilagg feed templates.
 *
 * Default template: zhilagg-feed.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An array of elements to display in view mode.
 */
function template_preprocess_zhilagg_feed(&$variables) {
  $feed = $variables['elements']['#zhilagg_feed'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
  $variables['full'] = $variables['elements']['#view_mode'] == 'full';
  $variables['title'] = $feed->label();
}
