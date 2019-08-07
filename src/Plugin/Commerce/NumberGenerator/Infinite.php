<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator;

use Drupal\Core\Annotation\Translation;

/**
 * Provides an infinite number generator.
 *
 * @CommerceNumberGenerator(
 *   id = "infinite",
 *   label = @Translation("Infinite (one single number, that is never reset, and incremented infinetly)"),
 * )
 */
class Infinite extends SequenceNumberGeneratorBase {


}
