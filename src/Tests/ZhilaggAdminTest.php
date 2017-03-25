<?php

namespace Drupal\zhilagg\Tests;

/**
 * Tests zhilagg admin pages.
 *
 * @group zhilagg
 */
class ZhilaggAdminTest extends ZhilaggTestBase {

  /**
   * Tests the settings form to ensure the correct default values are used.
   */
  public function testSettingsPage() {
    $this->drupalGet('admin/config');
    $this->clickLink('Zhilagg');
    $this->clickLink('Settings');
    // Make sure that test plugins are present.
    $this->assertText('Test fetcher');
    $this->assertText('Test parser');
    $this->assertText('Test processor');

    // Set new values and enable test plugins.
    $edit = array(
      'zhilagg_allowed_html_tags' => '<a>',
      'zhilagg_summary_items' => 10,
      'zhilagg_clear' => 3600,
      'zhilagg_teaser_length' => 200,
      'zhilagg_fetcher' => 'zhilagg_test_fetcher',
      'zhilagg_parser' => 'zhilagg_test_parser',
      'zhilagg_processors[zhilagg_test_processor]' => 'zhilagg_test_processor',
    );
    $this->drupalPostForm('admin/config/services/zhilagg/settings', $edit, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'));

    foreach ($edit as $name => $value) {
      $this->assertFieldByName($name, $value, format_string('"@name" has correct default value.', array('@name' => $name)));
    }

    // Check for our test processor settings form.
    $this->assertText(t('Dummy length setting'));
    // Change its value to ensure that settingsSubmit is called.
    $edit = array(
      'dummy_length' => 100,
    );
    $this->drupalPostForm('admin/config/services/zhilagg/settings', $edit, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'));
    $this->assertFieldByName('dummy_length', 100, '"dummy_length" has correct default value.');

    // Make sure settings form is still accessible even after uninstalling a module
    // that provides the selected plugins.
    $this->container->get('module_installer')->uninstall(array('zhilagg_test'));
    $this->resetAll();
    $this->drupalGet('admin/config/services/zhilagg/settings');
    $this->assertResponse(200);
  }

  /**
   * Tests the overview page.
   */
  function testOverviewPage() {
    $feed = $this->createFeed($this->getRSS091Sample());
    $this->drupalGet('admin/config/services/zhilagg');

    $result = $this->xpath('//table/tbody/tr');
    // Check if the amount of feeds in the overview matches the amount created.
    $this->assertEqual(1, count($result), 'Created feed is found in the overview');
    // Check if the fields in the table match with what's expected.
    $this->assertEqual($feed->label(), (string) $result[0]->td[0]->a);
    $count = $this->container->get('entity.manager')->getStorage('zhilagg_item')->getItemCount($feed);
    $this->assertEqual(\Drupal::translation()->formatPlural($count, '1 item', '@count items'), (string) $result[0]->td[1]);

    // Update the items of the first feed.
    $feed->refreshItems();
    $this->drupalGet('admin/config/services/zhilagg');
    $result = $this->xpath('//table/tbody/tr');
    // Check if the fields in the table match with what's expected.
    $this->assertEqual($feed->label(), (string) $result[0]->td[0]->a);
    $count = $this->container->get('entity.manager')->getStorage('zhilagg_item')->getItemCount($feed);
    $this->assertEqual(\Drupal::translation()->formatPlural($count, '1 item', '@count items'), (string) $result[0]->td[1]);
  }

}
