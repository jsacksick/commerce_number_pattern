<?php

namespace Drupal\Tests\commerce_number_pattern\Kernel\Plugin\Commerce\NumberGenerator;

use Drupal\commerce_number_pattern_test\Entity\EntityTestWithStore;
use Drupal\Tests\commerce_number_pattern\Kernel\NumberPatternKernelTestBase;

/**
 * Provides a base test class for number generator plugins.
 */
abstract class NumberGeneratorTestBase extends NumberPatternKernelTestBase {

  /**
   * The number generator manager.
   *
   * @var \Drupal\commerce_number_pattern\NumberGeneratorManager
   */
  protected $numberGeneratorManager;

  /**
   * A test entity.
   *
   * @var \Drupal\commerce_number_pattern_test\Entity\EntityTestWithStore
   */
  protected $entity;

  /**
   * The second store.
   *
   * @var \Drupal\commerce_store\Entity\StoreInterface
   */
  protected $store2;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->numberGeneratorManager = $this->container->get('plugin.manager.commerce_number_generator');

    $this->store2 = $this->createStore('Second store', 'admin2@example.com', 'online', FALSE);
    $this->entity = EntityTestWithStore::create([
      'store_id' => $this->store,
    ]);
    $this->entity->save();
  }

}
