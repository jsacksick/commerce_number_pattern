<?php

namespace Drupal\commerce_number_pattern\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;

interface NumberPatternInterface extends ConfigEntityInterface, EntityWithPluginCollectionInterface {

  /**
   * Gets the number pattern type (e.g commerce_order).
   *
   * @return string
   *   The number pattern type.
   */
  public function getType();

  /**
   * Gets the number generator plugin.
   *
   * @return \Drupal\commerce_number_pattern\Plugin\Commerce\NumberGenerator\NumberGeneratorInterface
   *   The number generator plugin.
   */
  public function getPlugin();

  /**
   * Gets the number generator plugin ID.
   *
   * @return string
   *   The number generator plugin ID.
   */
  public function getPluginId();

  /**
   * Sets the number generator plugin ID.
   *
   * @param string $plugin_id
   *   The number generator plugin ID.
   *
   * @return $this
   */
  public function setPluginId($plugin_id);

  /**
   * Gets the number generator plugin configuration.
   *
   * @return array
   *   The number generator plugin configuration.
   */
  public function getPluginConfiguration();

  /**
   * Sets the number generator plugin configuration.
   *
   * @param array $configuration
   *   The number generator plugin configuration.
   *
   * @return $this
   */
  public function setPluginConfiguration(array $configuration);

}
