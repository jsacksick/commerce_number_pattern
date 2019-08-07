<?php

namespace Drupal\commerce_number_pattern;

/**
 * Represents a sequence.
 */
final class Sequence {

  /**
   * The sequence.
   *
   * @var int
   */
  protected $sequence;

  /**
   * The sequence generated timestamp.
   *
   * @var int
   */
  protected $generated;

  /**
   * The sequence store ID.
   *
   * @var int
   */
  protected $storeId;

  /**
   * Constructs a new Sequence object.
   v
   * @param array $definition
   *   The definition.
   */
  public function __construct(array $definition) {
    foreach (['sequence', 'generated'] as $required_property) {
      if (empty($definition[$required_property])) {
        throw new \InvalidArgumentException(sprintf('Missing required property %s.', $required_property));
      }
    }
    $this->sequence = $definition['sequence'];
    $this->generated = $definition['generated'];
    $this->storeId = isset($definition['store_id']) ? $definition['store_id'] : NULL;
  }

  /**
   * Gets the sequence.
   *
   * @return int
   *   The sequence.
   */
  public function getSequence() {
    return $this->sequence;
  }

  /**
   * Gets the sequence generated timestamp.
   *
   * @return int
   *   The sequence generated timestamp.
   */
  public function getGeneratedTime() {
    return $this->generated;
  }

  /**
   * Gets the sequence store ID.
   *
   * @return int|null
   *   The sequence store ID, or null if it wasn't specified.
   */
  public function getStoreId() {
    return $this->storeId;
  }

}
