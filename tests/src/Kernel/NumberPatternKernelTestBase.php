<?php

namespace Drupal\Tests\commerce_number_pattern\Kernel;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;
use Drupal\Tests\commerce_number_pattern\Traits\NumberPatternTestTrait;

/**
 * Provides a base class for number pattern kernel tests.
 */
abstract class NumberPatternKernelTestBase extends CommerceKernelTestBase {

  use NumberPatternTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'entity_reference_revisions',
    'commerce_number_pattern_test',
    'commerce_number_pattern',
    'token',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Tests are crashing without the following line.
    $this->installConfig(['system']);
    $this->installSchema('commerce_number_pattern', ['commerce_number_pattern_sequence']);
    $this->installEntitySchema('entity_test_with_store');
  }

  /**
   * Changes the current time.
   *
   * @param int $new_time
   *   The new time.
   */
  protected function rewindTime($new_time) {
    $mock_time = $this->prophesize(TimeInterface::class);
    $mock_time->getCurrentTime()->willReturn($new_time);
    $mock_time->getRequestTime()->willReturn($new_time);
    $this->container->set('datetime.time', $mock_time->reveal());
  }

}
