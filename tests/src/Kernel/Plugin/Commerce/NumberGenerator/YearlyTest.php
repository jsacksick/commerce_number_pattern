<?php

namespace Drupal\Tests\commerce_number_pattern\Kernel\Plugin\Commerce\NumberGenerator;

/**
 * Tests the yearly number generator.
 *
 * @coversDefaultClass \Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator\Yearly
 * @group commerce_number_pattern
 */
class YearlyTest extends NumberGeneratorTestBase {

  /**
   * @covers ::generate
   */
  public function testGenerate() {
    $number_generator = $this->numberGeneratorManager->createInstance('yearly', [
      '_entity_id' => 'test',
      'per_store_sequence' => FALSE,
    ]);
    $current_year = date('Y');
    $this->assertEquals($current_year . '-1', $number_generator->generate($this->entity));
    $this->assertEquals($current_year . '-2', $number_generator->generate($this->entity));

    $this->entity->setStoreId($this->store2);
    $this->entity->save();
    $this->assertEquals($current_year . '-3', $number_generator->generate($this->entity));

    // Confirm that the sequence resets after a year.
    $this->rewindTime(strtotime('+1 years'));
    $next_year = $current_year + 1;
    $number_generator = $this->numberGeneratorManager->createInstance('yearly', [
      '_entity_id' => 'test',
      'per_store_sequence' => FALSE,
    ]);
    $this->assertEquals($next_year . '-1', $number_generator->generate($this->entity));
  }

}
