<?php

namespace Drupal\Tests\commerce_number_pattern\Kernel\Plugin\Commerce\NumberGenerator;

use Drupal\commerce_number_pattern\Sequence;

/**
 * Tests the infinite number generator.
 *
 * @coversDefaultClass \Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator\Infinite
 * @group commerce_number_pattern
 */
class InfiniteTest extends NumberGeneratorTestBase {

  /**
   * @covers ::shouldReset
   */
  public function testReset() {
    $number_generator = $this->numberGeneratorManager->createInstance('infinite');
    $definition = [
      'store_id' => $this->store->id(),
      'generated' => strtotime('today'),
      'sequence' => 10,
    ];
    $last_sequence = new Sequence($definition);
    $this->assertFalse($number_generator->shouldReset($last_sequence));
    $definition['generated'] = strtotime('-35 days');
    $last_sequence = new Sequence($definition);
    $this->assertFalse($number_generator->shouldReset($last_sequence));
  }

  /**
   * @covers ::generate
   * @covers ::getInitialSequence
   * @covers ::getLastSequence
   * @covers ::getNextSequence
   * @covers ::resetSequence
   */
  public function testGenerate() {
    /** @var \Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator\SupportsResettingSequencesInterface $number_generator */
    $number_generator = $this->numberGeneratorManager->createInstance('infinite', [
      '_entity_id' => 'test',
      'padding' => 0,
      'pattern' => 'INV-{sequence}',
      'perStoreSequence' => TRUE,
      'initialSequence' => 1000,
    ]);
    $this->assertEquals('INV-1000', $number_generator->generate($this->entity));
    $this->assertEquals('INV-1001', $number_generator->generate($this->entity));
    $number_generator->resetSequence();

    $this->assertEquals('INV-1000', $number_generator->generate($this->entity));
    $this->assertEquals('INV-1001', $number_generator->generate($this->entity));

    // Test the token replacement.
    $configuration = $number_generator->getConfiguration();
    $configuration['pattern'] = 'INV-[entity_test_with_store:store_id:target_id]-{sequence}';
    $number_generator->setConfiguration($configuration);
    $this->assertEquals('INV-1-1002', $number_generator->generate($this->entity));

    $this->entity->setStoreId($this->store2);
    $this->entity->save();
    $this->assertEquals('INV-2-1000', $number_generator->generate($this->entity));

    // Test the padding.
    $configuration = $number_generator->getConfiguration();
    $configuration['padding'] = 4;
    $configuration['initialSequence'] = 1;
    $number_generator->setConfiguration($configuration);
    $number_generator->resetSequence();
    $this->assertEquals('INV-2-0001', $number_generator->generate($this->entity));
  }

}
