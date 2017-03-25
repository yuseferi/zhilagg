<?php

namespace Drupal\zhilagg\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\zhilagg\FeedInterface;

/**
 * Defines the zhilagg feed entity class.
 *
 * @ContentEntityType(
 *   id = "zhilagg_feed",
 *   label = @Translation("Zhilagg feed"),
 *   handlers = {
 *     "storage" = "Drupal\zhilagg\FeedStorage",
 *     "storage_schema" = "Drupal\zhilagg\FeedStorageSchema",
 *     "view_builder" = "Drupal\zhilagg\FeedViewBuilder",
 *     "access" = "Drupal\zhilagg\FeedAccessControlHandler",
 *     "views_data" = "Drupal\zhilagg\ZhilaggFeedViewsData",
 *     "form" = {
 *       "default" = "Drupal\zhilagg\FeedForm",
 *       "delete" = "Drupal\zhilagg\Form\FeedDeleteForm",
 *       "delete_items" = "Drupal\zhilagg\Form\FeedItemsDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\zhilagg\FeedHtmlRouteProvider",
 *     },
 *   },
 *   links = {
 *     "canonical" = "/zhilagg/sources/{zhilagg_feed}",
 *     "edit-form" = "/zhilagg/sources/{zhilagg_feed}/configure",
 *     "delete-form" = "/zhilagg/sources/{zhilagg_feed}/delete",
 *   },
 *   field_ui_base_route = "zhilagg.admin_overview",
 *   base_table = "zhilagg_feed",
 *   render_cache = FALSE,
 *   entity_keys = {
 *     "id" = "fid",
 *     "label" = "title",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *   }
 * )
 */
class Feed extends ContentEntityBase implements FeedInterface {

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteItems() {
    \Drupal::service('zhilagg.items.importer')->delete($this);

    // Reset feed.
    $this->setLastCheckedTime(0);
    $this->setHash('');
    $this->setEtag('');
    $this->setLastModified(0);
    $this->save();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function refreshItems() {
    $success = \Drupal::service('zhilagg.items.importer')->refresh($this);

    // Regardless of successful or not, indicate that it has been checked.
    $this->setLastCheckedTime(REQUEST_TIME);
    $this->setQueuedTime(0);
    $this->save();

    return $success;
  }

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage, array &$values) {
    $values += array(
      'link' => '',
      'description' => '',
      'image' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function preDelete(EntityStorageInterface $storage, array $entities) {
    foreach ($entities as $entity) {
      // Notify processors to delete stored items.
      \Drupal::service('zhilagg.items.importer')->delete($entity);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);
    if (\Drupal::moduleHandler()->moduleExists('block')) {
      // Make sure there are no active blocks for these feeds.
      $ids = \Drupal::entityQuery('block')
        ->condition('plugin', 'zhilagg_feed_block')
        ->condition('settings.feed', array_keys($entities))
        ->execute();
      if ($ids) {
        $block_storage = \Drupal::entityManager()->getStorage('block');
        $block_storage->delete($block_storage->loadMultiple($ids));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $fields */
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['fid']->setLabel(t('Feed ID'))
      ->setDescription(t('The ID of the zhilagg feed.'));

    $fields['uuid']->setDescription(t('The zhilagg feed UUID.'));

    $fields['langcode']->setLabel(t('Language code'))
      ->setDescription(t('The feed language code.'));

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The name of the feed (or the name of the website providing the feed).'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->addConstraint('FeedTitle');

    $fields['url'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('URL'))
      ->setDescription(t('The fully-qualified URL of the feed.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'uri',
        'weight' => -3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->addConstraint('FeedUrl');

    $intervals = array(900, 1800, 3600, 7200, 10800, 21600, 32400, 43200, 64800, 86400, 172800, 259200, 604800, 1209600, 2419200);
    $period = array_map(array(\Drupal::service('date.formatter'), 'formatInterval'), array_combine($intervals, $intervals));
    $period[AGGREGATOR_CLEAR_NEVER] = t('Never');

    $fields['refresh'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Update interval'))
      ->setDescription(t('The length of time between feed updates. Requires a correctly configured cron maintenance task.'))
      ->setSetting('unsigned', TRUE)
      ->setRequired(TRUE)
      ->setSetting('allowed_values', $period)
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => -2,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['checked'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Checked'))
      ->setDescription(t('Last time feed was checked for new items, as Unix timestamp.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'timestamp_ago',
        'weight' => 1,
      ))
      ->setDisplayConfigurable('view', TRUE);

    $fields['queued'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Queued'))
      ->setDescription(t('Time when this feed was queued for refresh, 0 if not queued.'))
      ->setDefaultValue(0);

    $fields['link'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('URL'))
      ->setDescription(t('The link of the feed.'))
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'weight' => 4,
      ))
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Description'))
      ->setDescription(t("The parent website's description that comes from the @description element in the feed.", array('@description' => '<description>')));

    $fields['image'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Image'))
      ->setDescription(t('An image representing the feed.'));

    $fields['hash'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Hash'))
      ->setSetting('is_ascii', TRUE)
      ->setDescription(t('Calculated hash of the feed data, used for validating cache.'));

    $fields['etag'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Etag'))
      ->setDescription(t('Entity tag HTTP response header, used for validating cache.'));

    // This is updated by the fetcher and not when the feed is saved, therefore
    // it's a timestamp and not a changed field.
    $fields['modified'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Modified'))
      ->setDescription(t('When the feed was last modified, as a Unix timestamp.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl() {
    return $this->get('url')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getRefreshRate() {
    return $this->get('refresh')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCheckedTime() {
    return $this->get('checked')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getQueuedTime() {
    return $this->get('queued')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getWebsiteUrl() {
    return $this->get('link')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('description')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getImage() {
    return $this->get('image')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getHash() {
    return $this->get('hash')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getEtag() {
    return $this->get('etag')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastModified() {
    return $this->get('modified')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setUrl($url) {
    $this->set('url', $url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setRefreshRate($refresh) {
    $this->set('refresh', $refresh);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setLastCheckedTime($checked) {
    $this->set('checked', $checked);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setQueuedTime($queued) {
    $this->set('queued', $queued);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setWebsiteUrl($link) {
    $this->set('link', $link);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->set('description', $description);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setImage($image) {
    $this->set('image', $image);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setHash($hash) {
    $this->set('hash', $hash);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setEtag($etag) {
    $this->set('etag', $etag);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setLastModified($modified) {
    $this->set('modified', $modified);
    return $this;
  }

}
