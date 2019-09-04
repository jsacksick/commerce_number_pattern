<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberPattern;

use Drupal\commerce_number_pattern\Sequence;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Provides a yearly number pattern.
 *
 * @CommerceNumberPattern(
 *   id = "yearly",
 *   label = @Translation("Yearly (Reset every year)"),
 * )
 */
class Yearly extends SequentialNumberPatternBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'pattern' => '[current-date:custom:Y]-{sequence}',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  protected function shouldReset(Sequence $last_sequence) {
    $current_time = DrupalDateTime::createFromTimestamp($this->time->getCurrentTime());
    $generated_time = DrupalDateTime::createFromTimestamp($last_sequence->getGeneratedTime());
    // The sequence should be reset if the current year doesn't match the year
    // the last sequential number was generated.
    return $generated_time->format('Y') != $current_time->format('Y');
  }

}
