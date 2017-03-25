<?php

namespace Drupal\zhilagg;

use Drupal\views\EntityViewsData;

/**
 * Provides the views data for the zhilagg item entity type.
 */
class ZhilaggItemViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['zhilagg_item']['table']['base']['help'] = $this->t('Zhilagg items are imported from external RSS and Atom news feeds.');

    $data['zhilagg_item']['iid']['help'] = $this->t('The unique ID of the zhilagg item.');
    $data['zhilagg_item']['iid']['argument']['id'] = 'zhilagg_iid';
    $data['zhilagg_item']['iid']['argument']['name field'] = 'title';
    $data['zhilagg_item']['iid']['argument']['numeric'] = TRUE;

    $data['zhilagg_item']['title']['help'] = $this->t('The title of the zhilagg item.');
    $data['zhilagg_item']['title']['field']['default_formatter'] = 'zhilagg_title';

    $data['zhilagg_item']['link']['help'] = $this->t('The link to the original source URL of the item.');

    $data['zhilagg_item']['author']['help'] = $this->t('The author of the original imported item.');

    $data['zhilagg_item']['author']['field']['default_formatter'] = 'zhilagg_xss';

    $data['zhilagg_item']['guid']['help'] = $this->t('The guid of the original imported item.');

    $data['zhilagg_item']['description']['help'] = $this->t('The actual content of the imported item.');
    $data['zhilagg_item']['description']['field']['default_formatter'] = 'zhilagg_xss';
    $data['zhilagg_item']['description']['field']['click sortable'] = FALSE;

    $data['zhilagg_item']['timestamp']['help'] = $this->t('The date the original feed item was posted. (With some feeds, this will be the date it was imported.)');

    return $data;
  }

}
