<?php

namespace Drupal\Tests\commerce_number_pattern\Kernel\Plugin\Commerce\NumberGenerator;

/**
 * Tests the monthly invoice number generator.
 *
 * @coversDefaultClass \Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator\Monthly
 * @group commerce_number_pattern
 */
class MonthlyTest extends NumberGeneratorTestBase {

  /**
   * @covers ::generate
   */
  public function testGenerate() {
    $number_generator = $this->numberGeneratorManager->createInstance('monthly', [
      '_entity_id' => 'test',
    ]);
    $current_date = date('Y-m');
    $this->assertEquals($current_date . '-1', $number_generator->generate($this->entity));
    $this->assertEquals($current_date . '-2', $number_generator->generate($this->entity));
    $configuration = $number_generator->getConfiguration();
    $configuration['padding'] = 5;
    $configuration['pattern'] = '[current-date:custom:Y-m]-[entity_test_with_store:store_id:target_id]-{sequence}';
    $number_generator->setConfiguration($configuration);
    $this->assertEquals($current_date . '-1-00003', $number_generator->generate($this->entity));

    // Confirm that the sequence resets after a month.
    $this->rewindTime(strtotime('+1 month'));
    $next_month = date('m') + 1;
    $expected_date = date('Y') . '-' . $next_month;
    $number_generator = $this->numberGeneratorManager->createInstance('monthly', [
      '_entity_id' => 'test',
    ]);
    $this->assertEquals($expected_date . '-1', $number_generator->generate($this->entity));
  }

}
