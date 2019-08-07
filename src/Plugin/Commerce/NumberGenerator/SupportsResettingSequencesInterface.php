<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator;

use Drupal\commerce_number_pattern\Sequence;

/**
 * Defines the interface for number generators which support resetting sequences.
 */
interface SupportsResettingSequencesInterface extends SupportsSequenceInterface {

  /**
   * Gets whether the sequence should reset.
   *
   * @param \Drupal\commerce_number_pattern\Sequence $last_sequence
   *   The last sequence.
   *
   * @return bool
   *   Whether the sequence should reset.
   */
  public function shouldReset(Sequence $last_sequence);

  /**
   * Resets the sequence
   */
  public function resetSequence();

}
