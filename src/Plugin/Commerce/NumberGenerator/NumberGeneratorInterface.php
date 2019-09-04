<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Core\Entity\ContentEntityInterface;

interface NumberGeneratorInterface extends ConfigurableInterface {

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
