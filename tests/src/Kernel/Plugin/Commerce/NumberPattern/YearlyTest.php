<?php

namespace Drupal\Tests\commerce_number_pattern\Kernel\Plugin\Commerce\NumberPattern;

use Drupal\commerce_number_pattern_test\Entity\EntityTestWithStore;
use Drupal\Tests\commerce_number_pattern\Kernel\NumberPatternKernelTestBase;

/**
 * Tests the yearly number pattern.
 *
 * @coversDefaultClass \Drupal\commerce_number_pattern\Plugin\Commerce\NumberPattern\Yearly
 * @group commerce_number_pattern
 */
class YearlyTest extends NumberPatternKernelTestBase {

  /**
   * @covers ::generate
   */
  public function testGenerate() {
    $entity = EntityTestWithStore::create([
      'store_id' => $this->store,
    ]);
    $entity->save();

    $number_pattern_plugin = $this->pluginManager->createInstance('yearly', [
      '_entity_id' => 'test',
      'per_store_sequence' => FALSE,
    ]);
    $current_year = date('Y');
    $this->assertEquals($current_year . '-1', $number_pattern_plugin->generate($entity));
    $this->assertEquals($current_year . '-2', $number_pattern_plugin->generate($entity));

    $second_store = $this->createStore('Second store', 'admin2@example.com', 'online', FALSE);
    $entity->setStoreId($second_store->id());
    $entity->save();
    $this->assertEquals($current_year . '-3', $number_pattern_plugin->generate($entity));

    // Confirm that the sequence resets after a year.
    $this->rewindTime(strtotime('+1 years'));
    $next_year = $current_year + 1;
    $number_pattern_plugin = $this->pluginManager->createInstance('yearly', [
      '_entity_id' => 'test',
      'per_store_sequence' => FALSE,
    ]);
    $this->assertEquals($next_year . '-1', $number_pattern_plugin->generate($entity));
  }

}
