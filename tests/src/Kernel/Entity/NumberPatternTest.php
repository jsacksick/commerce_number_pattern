<?php

namespace Drupal\Tests\commerce_number_pattern\Kernel\Entity;

use Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator\NumberGeneratorInterface;
use Drupal\Tests\commerce_number_pattern\Kernel\NumberPatternKernelTestBase;

/**
 * Tests the number pattern entity.
 *
 * @coversDefaultClass \Drupal\commerce_number_pattern\Entity\NumberPattern
 *
 * @group commerce_number_pattern
 */
class NumberPatternTest extends NumberPatternKernelTestBase {

  /**
   * @covers ::id
   * @covers ::label
   * @covers ::getType
   * @covers ::getPlugin
   * @covers ::getPluginId
   * @covers ::getPluginConfiguration
   */
  public function testNumberPattern() {
    $values = [
      'type' => 'entity_test_with_store',
      'plugin' => 'monthly',
      'configuration' => [
        'initial_sequence' => 100,
        'pattern' => '[current-date:custom:Y-m]-{number}',
        'per_store_sequence' => TRUE,
        'padding' => 0,
      ],
    ];
    $number_pattern = $this->createNumberPattern('test_id', 'Test label', $values);
    $this->assertEquals('test_id', $number_pattern->id());
    $this->assertEquals('Test label', $number_pattern->label());
    $this->assertEquals($values['type'], $number_pattern->getType());

    $number_generator = $number_pattern->getPlugin();
    $this->assertInstanceOf(NumberGeneratorInterface::class, $number_generator);
    $this->assertEquals('monthly', $number_generator->getPluginId());
    $this->assertEquals($number_pattern->getPluginConfiguration(), $number_generator->getConfiguration());
    $number_pattern->setPluginConfiguration([
      'pattern' => 'INV-[current-date:custom:Y-m]-{number}',
      'padding' => 5,
    ]);
    $this->assertEquals([
      'pattern' => 'INV-[current-date:custom:Y-m]-{number}',
      'padding' => 5,
    ], $number_pattern->getPluginConfiguration());

    $number_pattern->setPluginId('yearly');
    $this->assertEquals('yearly', $number_pattern->getPluginId());
    $this->assertEmpty($number_pattern->getPluginConfiguration());
  }

}
