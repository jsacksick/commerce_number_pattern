<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberPattern;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Utility\Token;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class for number pattern plugins.
 */
abstract class NumberPatternBase extends PluginBase implements NumberPatternInterface, ContainerFactoryPluginInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The ID of the parent config entity.
   *
   * Not available while the plugin is being configured.
   *
   * @var string
   */
  protected $entityId;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  protected $entityTypeManager;

  /**
   * The lock backend.
   *
   * @var \Drupal\Core\Lock\LockBackendInterface
   */
  protected $lock;

  /**
   * The time.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * Constructs a new NumberPatternBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Lock\LockBackendInterface $lock
   *   The lock backend.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $connection, EntityTypeManagerInterface $entity_type_manager, LockBackendInterface $lock, TimeInterface $time, Token $token) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->connection = $connection;
    if (array_key_exists('_entity_id', $configuration)) {
      $this->entityId = $configuration['_entity_id'];
      unset($configuration['_entity_id']);
    }
    $this->entityTypeManager = $entity_type_manager;
    $this->lock = $lock;
    $this->time = $time;
    $this->token = $token;
    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('entity_type.manager'),
      $container->get('lock'),
      $container->get('datetime.time'),
      $container->get('token')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function generate(ContentEntityInterface $entity) {
    return $this->token->replace($this->configuration['pattern'], [$entity->getEntityTypeId() => $entity]);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'pattern' => '',
      'padding' => 0,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $entity_type_id = $form_state->getValue('type');
    $token_types = $entity_type_id ? [$entity_type_id] : [];
    $form['pattern'] = [
      '#title' => $this->t('Pattern'),
      '#type' => 'textfield',
      '#default_value' => $this->configuration['pattern'],
      '#required' => TRUE,
      '#element_validate' => ['token_element_validate'],
      '#token_types' => $token_types,
    ];
    $form['pattern_help'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => $token_types,
    ];
    $form['padding'] = [
      '#type' => 'number',
      '#title' => $this->t('Padding'),
      '#description' => $this->t('Pad the number with leading zeroes. Example: a value of 6 will output 52 as 000052.'),
      '#default_value' => $this->configuration['padding'],
      '#min' => 0,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue($form['#parents']);
    if (strpos($values['pattern'], '{number}') === FALSE) {
      $form_state->setError($form['pattern'], t('Missing the required placeholder {number}.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);

      $this->configuration = [];
      $this->configuration['pattern'] = $values['pattern'];
      $this->configuration['padding'] = $values['padding'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = NestedArray::mergeDeep($this->defaultConfiguration(), $configuration);
  }

}
