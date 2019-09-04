<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator;

/**
 * Defines the interface for number generators which support resetting sequences.
 */
interface SupportsResettingSequencesInterface extends SupportsSequenceInterface {

  /**
   * Resets the sequence.
   */
  public function resetSequence();

}
