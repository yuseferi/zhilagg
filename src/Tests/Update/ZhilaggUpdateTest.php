<?php

namespace Drupal\zhilagg\Tests\Update;

use Drupal\system\Tests\Update\UpdatePathTestBase;

/**
 * Tests that node settings are properly updated during database updates.
 *
 * @group zhilagg
 */
class ZhilaggUpdateTest extends UpdatePathTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setDatabaseDumpFiles() {
    $this->databaseDumpFiles = [
      __DIR__ . '/../../../../system/tests/fixtures/update/drupal-8.filled.standard.php.gz',
    ];
  }

  /**
   * Tests that the 'Source feed' field is required.
   *
   * @see zhilagg_update_8200()
   */
  public function testSourceFeedRequired() {
    // Check that the 'fid' field is not required prior to the update.
    $field_definition = \Drupal::entityDefinitionUpdateManager()->getFieldStorageDefinition('fid', 'zhilagg_item');
    $this->assertFalse($field_definition->isRequired());

    // Run updates.
    $this->runUpdates();

    // Check that the 'fid' field is now required.
    $field_definition = \Drupal::entityDefinitionUpdateManager()->getFieldStorageDefinition('fid', 'zhilagg_item');
    $this->assertTrue($field_definition->isRequired());
  }

}
