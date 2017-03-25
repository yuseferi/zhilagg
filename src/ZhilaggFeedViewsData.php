<?php

namespace Drupal\zhilagg;

use Drupal\views\EntityViewsData;

/**
 * Provides the views data for the zhilagg feed entity type.
 */
class ZhilaggFeedViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['zhilagg_feed']['table']['join'] = array(
      'zhilagg_item' => array(
        'left_field' => 'fid',
        'field' => 'fid',
      ),
    );

    $data['zhilagg_feed']['fid']['help'] = $this->t('The unique ID of the zhilagg feed.');
    $data['zhilagg_feed']['fid']['argument']['id'] = 'zhilagg_fid';
    $data['zhilagg_feed']['fid']['argument']['name field'] = 'title';
    $data['zhilagg_feed']['fid']['argument']['numeric'] = TRUE;

    $data['zhilagg_feed']['fid']['filter']['id'] = 'numeric';

    $data['zhilagg_feed']['title']['help'] = $this->t('The title of the zhilagg feed.');
    $data['zhilagg_feed']['title']['field']['default_formatter'] = 'zhilagg_title';

    $data['zhilagg_feed']['argument']['id'] = 'string';

    $data['zhilagg_feed']['url']['help'] = $this->t('The fully-qualified URL of the feed.');

    $data['zhilagg_feed']['link']['help'] = $this->t('The link to the source URL of the feed.');

    $data['zhilagg_feed']['checked']['help'] = $this->t('The date the feed was last checked for new content.');

    $data['zhilagg_feed']['description']['help'] = $this->t('The description of the zhilagg feed.');
    $data['zhilagg_feed']['description']['field']['click sortable'] = FALSE;

    $data['zhilagg_feed']['modified']['help'] = $this->t('The date of the most recent new content on the feed.');

    return $data;
  }

}
