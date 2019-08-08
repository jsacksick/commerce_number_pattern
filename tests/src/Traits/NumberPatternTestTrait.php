<?php

namespace Drupal\Tests\commerce_number_pattern\Traits;

use Drupal\commerce_number_pattern\Entity\NumberPattern;

/**
 * Helper for number pattern test classes.
 */
trait NumberPatternTestTrait {

  /**
   * Creates a test number pattern.
   *
   * @param string $id
   *   (optional) The number pattern machine name.
   * @param string $label
   *   (optional) The number patternlabel.
   * @param array $values
   *   (optional) An array of values to set, keyed by property name.
   *
   * @return \Drupal\commerce_number_pattern\Entity\NumberPatternInterface
   *   The created number pattern type.
   */
  protected function createNumberPattern($id = NULL, $label = NULL, array $values = []) {
    $id = !empty($id) ? $id : $this->randomMachineName();
    $label = !empty($label) ? $label : $this->randomMachineName();
    $values += [
      'id' => $id,
      'label' => $label,
    ];
    $number_pattern = NumberPattern::create($values);
    $number_pattern->save();
    return $number_pattern;
  }

}
