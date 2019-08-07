<?php

namespace Drupal\Tests\commerce_number_pattern\Kernel\Plugin\Commerce\NumberGenerator;

use Drupal\commerce_number_pattern\Sequence;

/**
 * Tests the monthly invoice number generator.
 *
 * @coversDefaultClass \Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator\Monthly
 * @group commerce_number_pattern
 */
class MonthlyTest extends NumberGeneratorTestBase {

  /**
   * @covers ::shouldReset
   */
  public function testReset() {
    $number_generator = $this->numberGeneratorManager->createInstance('monthly');
    $definition = [
      'store_id' => $this->store->id(),
      'generated' => strtotime('-35 days'),
      'sequence' => 10,
    ];
    $last_sequence = new Sequence($definition);
    $this->assertTrue($number_generator->shouldReset($last_sequence));
    $definition['generated'] = strtotime('today');
    $last_sequence = new Sequence($definition);
    $this->assertFalse($number_generator->shouldReset($last_sequence));
  }

  /**
   * @covers ::generate
   */
  public function testGenerate() {
    /** @var \Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator\SupportsResettingSequencesInterface $number_generator */
    $number_generator = $this->numberGeneratorManager->createInstance('monthly', [
      '_entity_id' => 'test',
    ]);
    $current_month = date('Y-m');
    $this->assertEquals($current_month . '-1', $number_generator->generate($this->entity));
    $this->assertEquals($current_month . '-2', $number_generator->generate($this->entity));
    $configuration = $number_generator->getConfiguration();
    $configuration['padding'] = 5;
    $number_generator->setConfiguration($configuration);
    $this->assertEquals($current_month . '-00003', $number_generator->generate($this->entity));
  }

}
