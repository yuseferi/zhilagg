<?php

namespace Drupal\zhilagg\Tests;

/**
 * Tests the display of a feed on the Zhilagg list page.
 *
 * @group zhilagg
 */
class FeedAdminDisplayTest extends ZhilaggTestBase {

  /**
   * Tests the "Next update" and "Last update" fields.
   */
  public function testFeedUpdateFields() {
    // Create scheduled feed.
    $scheduled_feed = $this->createFeed(NULL, array('refresh' => '900'));

    $this->drupalGet('admin/config/services/zhilagg');
    $this->assertResponse(200, 'Zhilagg feed overview page exists.');

    // The scheduled feed shows that it has not been updated yet and is
    // scheduled.
    $this->assertText('never', 'The scheduled feed has not been updated yet.  Last update shows "never".');
    $this->assertText('imminently', 'The scheduled feed has not been updated yet. Next update shows "imminently".');
    $this->assertNoText('ago', 'The scheduled feed has not been updated yet.  Last update does not show "x x ago".');
    $this->assertNoText('left', 'The scheduled feed has not been updated yet.  Next update does not show "x x left".');

    $this->updateFeedItems($scheduled_feed);
    $this->drupalGet('admin/config/services/zhilagg');

    // After the update, an interval should be displayed on both last updated
    // and next update.
    $this->assertNoText('never', 'The scheduled feed has been updated. Last updated changed.');
    $this->assertNoText('imminently', 'The scheduled feed has been updated. Next update changed.');
    $this->assertText('ago', 'The scheduled feed been updated.  Last update shows "x x ago".');
    $this->assertText('left', 'The scheduled feed has been updated. Next update shows "x x left".');

    // Delete scheduled feed.
    $this->deleteFeed($scheduled_feed);

    // Create non-scheduled feed.
    $non_scheduled_feed = $this->createFeed(NULL, array('refresh' => '0'));

    $this->drupalGet('admin/config/services/zhilagg');
    // The non scheduled feed shows that it has not been updated yet.
    $this->assertText('never', 'The non scheduled feed has not been updated yet.  Last update shows "never".');
    $this->assertNoText('imminently', 'The non scheduled feed does not show "imminently" as next update.');
    $this->assertNoText('ago', 'The non scheduled feed has not been updated. It does not show "x x ago" as last update.');
    $this->assertNoText('left', 'The feed is not scheduled. It does not show a timeframe "x x left" for next update.');

    $this->updateFeedItems($non_scheduled_feed);
    $this->drupalGet('admin/config/services/zhilagg');

    // After the feed update, we still need to see "never" as next update label.
    // Last update will show an interval.
    $this->assertNoText('imminently', 'The updated non scheduled feed does not show "imminently" as next update.');
    $this->assertText('never', 'The updated non scheduled feed still shows "never" as next update.');
    $this->assertText('ago', 'The non scheduled feed has been updated. It shows "x x ago" as last update.');
    $this->assertNoText('left', 'The feed is not scheduled. It does not show a timeframe "x x left" for next update.');
  }

}
