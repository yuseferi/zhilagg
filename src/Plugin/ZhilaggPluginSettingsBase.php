<?php

namespace Drupal\zhilagg\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Base class for zhilagg plugins that implement settings forms.
 *
 * @see \Drupal\zhilagg\Annotation\ZhilaggParser
 * @see \Drupal\zhilagg\Annotation\ZhilaggFetcher
 * @see \Drupal\zhilagg\Annotation\ZhilaggProcessor
 * @see \Drupal\zhilagg\Plugin\ZhilaggPluginManager
 * @see \Drupal\zhilagg\Plugin\FetcherInterface
 * @see \Drupal\zhilagg\Plugin\ProcessorInterface
 * @see \Drupal\zhilagg\Plugin\ParserInterface
 * @see plugin_api
 */
abstract class ZhilaggPluginSettingsBase extends PluginBase implements PluginFormInterface, ConfigurablePluginInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return array();
  }

}
