<?php

namespace Drupal\Tests\commerce_number_pattern\Kernel\Plugin\Commerce\NumberGenerator;

use Drupal\commerce_number_pattern\Sequence;

/**
 * Tests the yearly number generator.
 *
 * @coversDefaultClass \Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator\Yearly
 * @group commerce_number_pattern
 */
class YearlyTest extends NumberGeneratorTestBase {

  /**
   * @covers ::shouldReset
   */
  public function testReset() {
    $number_generator = $this->numberGeneratorManager->createInstance('yearly');
    $definition = [
      'store_id' => $this->store->id(),
      'generated' => strtotime('today'),
      'sequence' => 10,
    ];
    $last_sequence = new Sequence($definition);
    $this->assertFalse($number_generator->shouldReset($last_sequence));
    $this->rewindTime(strtotime('+2 years'));
    $number_generator = $this->numberGeneratorManager->createInstance('yearly');
    $this->assertTrue($number_generator->shouldReset($last_sequence));
  }

  /**
   * @covers ::generate
   */
  public function testGenerate() {
    $number_generator = $this->numberGeneratorManager->createInstance('yearly', [
      '_entity_id' => 'test',
      'perStoreSequence' => FALSE,
    ]);
    $current_year = date('Y');
    $this->assertEquals($current_year . '-1', $number_generator->generate($this->entity));
    $this->assertEquals($current_year . '-2', $number_generator->generate($this->entity));

    $this->entity->setStoreId($this->store2);
    $this->entity->save();
    $this->assertEquals($current_year . '-3', $number_generator->generate($this->entity));
  }

}
