<?php

namespace Drupal\Tests\commerce_number_pattern\Kernel\Plugin\Commerce\NumberPattern;

use Drupal\commerce_number_pattern_test\Entity\EntityTestWithStore;
use Drupal\Tests\commerce_number_pattern\Kernel\NumberPatternKernelTestBase;

/**
 * Tests the infinite number pattern.
 *
 * @coversDefaultClass \Drupal\commerce_number_pattern\Plugin\Commerce\NumberPattern\Infinite
 * @group commerce_number_pattern
 */
class InfiniteTest extends NumberPatternKernelTestBase {

  /**
   * @covers ::generate
   * @covers ::getInitialSequence
   * @covers ::getCurrentSequence
   * @covers ::getNextSequence
   * @covers ::resetSequence
   */
  public function testGenerate() {
    $entity = EntityTestWithStore::create([
      'store_id' => $this->store,
    ]);
    $entity->save();

    $number_pattern_plugin = $this->pluginManager->createInstance('infinite', [
      '_entity_id' => 'test',
      'padding' => 0,
      'pattern' => 'INV-{number}',
      'per_store_sequence' => TRUE,
      'initial_number' => 1000,
    ]);
    $this->assertEquals('INV-1000', $number_pattern_plugin->generate($entity));
    $this->assertEquals('INV-1001', $number_pattern_plugin->generate($entity));
    $number_pattern_plugin->resetSequence();

    $this->assertEquals('INV-1000', $number_pattern_plugin->generate($entity));
    $this->assertEquals('INV-1001', $number_pattern_plugin->generate($entity));

    // Test the token replacement.
    $configuration = $number_pattern_plugin->getConfiguration();
    $configuration['pattern'] = 'INV-[entity_test_with_store:store_id:target_id]-{number}';
    $number_pattern_plugin->setConfiguration($configuration);
    $this->assertEquals('INV-1-1002', $number_pattern_plugin->generate($entity));

    // Confirm that each store gets its own sequence.
    $second_store = $this->createStore('Second store', 'admin2@example.com', 'online', FALSE);
    $entity->setStoreId($second_store->id());
    $entity->save();
    $this->assertEquals('INV-2-1000', $number_pattern_plugin->generate($entity));

    // Test the padding.
    $configuration = $number_pattern_plugin->getConfiguration();
    $configuration['padding'] = 4;
    $configuration['initial_number'] = 1;
    $number_pattern_plugin->setConfiguration($configuration);
    $number_pattern_plugin->resetSequence();
    $this->assertEquals('INV-2-0001', $number_pattern_plugin->generate($entity));
  }

}
