<?php

namespace Drupal\commerce_number_pattern\Plugin\Commerce\NumberPattern;

use Drupal\commerce_number_pattern\Sequence;
use Drupal\commerce_store\Entity\EntityStoreInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\Core\Utility\Token;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class for number pattern plugins which support sequences.
 */
abstract class SequentialNumberPatternBase extends NumberPatternBase implements SupportsSequenceInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
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
   * Constructs a new SequentialNumberPatternBase object.
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
    parent::__construct($configuration, $plugin_id, $plugin_definition, $token);

    $this->connection = $connection;
    $this->entityTypeManager = $entity_type_manager;
    $this->lock = $lock;
    $this->time = $time;
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
  public function defaultConfiguration() {
    return [
      'pattern' => '{sequence}',
      'per_store_sequence' => TRUE,
      'initial_sequence' => 1,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function generate(ContentEntityInterface $entity) {
    $next_sequence = $this->getNextSequence($entity);
    $sequence = $next_sequence->getSequence();
    if ($this->configuration['padding'] > 0) {
      $sequence = str_pad($sequence, $this->configuration['padding'], '0', STR_PAD_LEFT);
    }
    $sequence = str_replace('{sequence}', $sequence, $this->configuration['pattern']);
    return $this->token->replace($sequence, [$entity->getEntityTypeId() => $entity]);
  }

  /**
   * {@inheritdoc}
   */
  public function getInitialSequence(ContentEntityInterface $entity) {
    return new Sequence([
      'generated' => $this->time->getCurrentTime(),
      'sequence' => $this->configuration['initial_sequence'],
      'store_id' => $this->getStoreId($entity),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getLastSequence(ContentEntityInterface $entity) {
    $query = $this->connection->select('commerce_number_pattern_sequence', 'cnps');
    $query->fields('cnps', ['store_id', 'sequence', 'generated']);
    $query
      ->condition('entity_id', $this->entityId)
      ->condition('store_id', $this->getStoreId($entity));
    $result = $query->execute()->fetchAssoc();

    if (empty($result)) {
      return NULL;
    }

    return new Sequence([
      'store_id' => $result['store_id'],
      'generated' => $result['generated'],
      'sequence' => $result['sequence'],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getNextSequence(ContentEntityInterface $entity) {
    $lock_name = "commerce_number_pattern.plugin.{$this->entityId}";
    while (!$this->lock->acquire($lock_name)) {
      $this->lock->wait($lock_name);
    }
    $sequence = $this->getLastSequence($entity);
    $store_id = $this->getStoreId($entity);
    if (!$sequence || $this->shouldReset($sequence)) {
      $sequence = $this->getInitialSequence($entity);
    }
    else {
      $sequence = new Sequence([
        'generated' => $this->time->getCurrentTime(),
        'sequence' => $sequence->getSequence() + 1,
        'store_id' => $store_id,
      ]);
    }
    $this->connection->merge('commerce_number_pattern_sequence')
      ->fields([
        'entity_id' => $this->entityId,
        'sequence' => $sequence->getSequence(),
        'generated' => $sequence->getGeneratedTime(),
        'store_id' => $store_id,
      ])
      ->keys([
        'entity_id' => $this->entityId,
        'store_id' => $store_id,
      ])
      ->execute();
    $this->lock->release($lock_name);

    return $sequence;
  }

  /**
   * {@inheritdoc}
   */
  public function resetSequence() {
    return $this->connection->delete('commerce_number_pattern_sequence')
      ->condition('entity_id', $this->entityId)
      ->execute();
  }

  /**
   * Gets whether the sequence should reset.
   *
   * @param \Drupal\commerce_number_pattern\Sequence $last_sequence
   *   The last sequence.
   *
   * @return bool
   *   Whether the sequence should reset.
   */
  protected function shouldReset(Sequence $last_sequence) {
    return FALSE;
  }

  /**
   * Gets the store_id to use for the sequence.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The content entity.
   *
   * @return int
   *   The store ID.
   */
  protected function getStoreId(ContentEntityInterface $entity) {
    $store_id = 0;

    if (!empty($this->configuration['per_store_sequence']) && $entity instanceof EntityStoreInterface) {
      $store_id = $entity->getStoreId();
    }

    return $store_id;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['initial_sequence'] = [
      '#type' => 'number',
      '#title' => $this->t('Initial sequence'),
      '#description' => $this->t('Overrides the initial sequence (Defaults to 1).'),
      '#default_value' => $this->configuration['initial_sequence'],
      '#min' => 1,
    ];
    $entity_type_id = $form_state->getValue('type');

    if (!empty($entity_type_id)) {
      $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);

      // The per store sequence setting should only appear for entity type
      // that implements \Drupal\commerce_store\Entity\EntityStoreInterface.
      if ($entity_type->entityClassImplements(EntityStoreInterface::class)) {
        $form['per_store_sequence'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Generate a unique sequence for each store'),
          '#description' => $this->t('Ensures that numbers are not shared between stores.'),
          '#default_value' => $this->configuration['per_store_sequence'],
        ];
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue($form['#parents']);
    if (strpos($values['pattern'], '{sequence}') === FALSE) {
      $form_state->setError($form['pattern'], t('Missing the required placeholder {sequence}.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['initial_sequence'] = $values['initial_sequence'];
      if (isset($values['per_store_sequence'])) {
        $this->configuration['per_store_sequence'] = $values['per_store_sequence'];
      }
    }
  }

}
