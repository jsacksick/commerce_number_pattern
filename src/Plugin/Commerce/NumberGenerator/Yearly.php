<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator;

use Drupal\commerce_number_pattern\Sequence;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Provides a yearly number generator.
 *
 * @CommerceNumberGenerator(
 *   id = "yearly",
 *   label = @Translation("Yearly (Reset every year)"),
 * )
 */
class Yearly extends SequenceNumberGeneratorBase {

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
  public function shouldReset(Sequence $last_sequence) {
    $current_time = DrupalDateTime::createFromTimestamp($this->time->getCurrentTime());
    $generated_time = DrupalDateTime::createFromTimestamp($last_sequence->getGeneratedTime());
    // The sequence should be reset if the current year doesn't match the year
    // the last sequential number was generated.
    return $generated_time->format('Y') != $current_time->format('Y');
  }

}
