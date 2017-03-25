<?php

namespace Drupal\zhilagg\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'zhilagg_title' formatter.
 *
 * @FieldFormatter(
 *   id = "zhilagg_title",
 *   label = @Translation("Zhilagg title"),
 *   description = @Translation("Formats an zhilagg item or feed title with an optional link."),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class ZhilaggTitleFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $options = parent::defaultSettings();

    $options['display_as_link'] = TRUE;
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['display_as_link'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Link to URL'),
      '#default_value' => $this->getSetting('display_as_link'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    if ($items->getEntity()->getEntityTypeId() == 'zhilagg_feed') {
      $url_string = $items->getEntity()->getUrl();
    }
    else {
      $url_string = $items->getEntity()->getLink();
    }

    foreach ($items as $delta => $item) {
      if ($this->getSetting('display_as_link') && $url_string) {
        $elements[$delta] = [
            '#type' => 'link',
            '#title' => $item->value,
            '#url' => Url::fromUri($url_string),
        ];
      }
      else {
        $elements[$delta] = ['#markup' => $item->value];
      }
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return (($field_definition->getTargetEntityTypeId() === 'zhilagg_item' || $field_definition->getTargetEntityTypeId() === 'zhilagg_feed') && $field_definition->getName() === 'title');
  }

}
