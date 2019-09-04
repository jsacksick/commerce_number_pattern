<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberPattern;

use Drupal\Core\Entity\ContentEntityInterface;

interface SupportsSequenceInterface extends NumberPatternInterface {

  /**
   * Gets the initial sequence.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity.
   *
   * @return \Drupal\commerce_number_pattern\Sequence
   *   The initial sequence.
   */
  public function getInitialSequence(ContentEntityInterface $entity);

  /**
   * Gets the last sequence.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity.
   *
   * @return \Drupal\commerce_number_pattern\Sequence|null
   *   The last sequence, or NULL if no sequence was found in DB.
   */
  public function getLastSequence(ContentEntityInterface $entity);

  /**
   * Gets the next sequence.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity.
   *
   * @return \Drupal\commerce_number_pattern\Sequence
   *   The next sequence.
   */
  public function getNextSequence(ContentEntityInterface $entity);

  /**
   * Resets the sequence.
   */
  public function resetSequence();

}