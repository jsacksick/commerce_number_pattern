<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Core\Entity\ContentEntityInterface;

interface NumberGeneratorInterface extends ConfigurableInterface {

  /**
   * Gets the number generator pattern.
   *
   * @return string
   *   The number generator pattern.
   */
  public function getPattern();

  /**
   * Gets the number generator padding.
   *
   * @return int
   *   The number generator padding.
   */
  public function getPadding();

  /**
   * Generates a number for the given content entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The content entity interface to generate a number for.
   *
   * @return string
   *   The generated number.
   */
  public function generate(ContentEntityInterface $entity);

}
