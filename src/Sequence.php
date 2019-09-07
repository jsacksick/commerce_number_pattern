<?php

namespace Drupal\commerce_number_pattern;

/**
 * Represents a sequence.
 */
final class Sequence {

  /**
   * The number.
   *
   * @var int
   */
  protected $number;

  /**
   * The generated timestamp.
   *
   * @var int
   */
  protected $generated;

  /**
   * The store ID.
   *
   * @var int
   */
  protected $storeId;

  /**
   * Constructs a new Sequence object.
   *
   * @param array $definition
   *   The definition.
   */
  public function __construct(array $definition) {
    foreach (['number', 'generated'] as $required_property) {
      if (empty($definition[$required_property])) {
        throw new \InvalidArgumentException(sprintf('Missing required property %s.', $required_property));
      }
    }
    $this->number = $definition['number'];
    $this->generated = $definition['generated'];
    $this->storeId = isset($definition['store_id']) ? $definition['store_id'] : NULL;
  }

  /**
   * Gets the number.
   *
   * @return int
   *   The number.
   */
  public function getNumber() {
    return $this->number;
  }

  /**
   * Gets the generated timestamp.
   *
   * @return int
   *   The generated timestamp.
   */
  public function getGeneratedTime() {
    return $this->generated;
  }

  /**
   * Gets the store ID.
   *
   * @return int|null
   *   The store ID, or null if it wasn't specified.
   */
  public function getStoreId() {
    return $this->storeId;
  }

}
