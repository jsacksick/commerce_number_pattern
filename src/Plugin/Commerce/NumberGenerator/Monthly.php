<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator;

use Drupal\commerce_number_pattern\Sequence;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Provides a monthly number generator.
 *
 * @CommerceNumberGenerator(
 *   id = "monthly",
 *   label = @Translation("Monthly (Reset every month)"),
 * )
 */
class Monthly extends SequenceNumberGeneratorBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'pattern' => '[current-date:custom:Y-m]-{sequence}',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  protected function shouldReset(Sequence $last_sequence) {
    $current_time = DrupalDateTime::createFromTimestamp($this->time->getCurrentTime());
    $generated_time = DrupalDateTime::createFromTimestamp($last_sequence->getGeneratedTime());

    // The sequence should be reset if the last sequential number was not
    // generated during the same month.
    if (($generated_time->format('Y') != $current_time->format('Y')) ||
      ($generated_time->format('m') != $current_time->format('m'))) {
      return TRUE;
    }

    return FALSE;
  }

}
