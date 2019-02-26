<?php

namespace Drupal\salesforce_rest\Config;

use \Drupal\salesforce_rest\Contracts\ImmutableInterface;

final class ArrayConfig implements ImmutableInterface {

  /**
   * @var \ArrayObject
   */
  private $config;

  /**
   * @param array $config
   */
  public function __construct(array $config) {
    $this->config = new \ArrayObject($config);
  }

  /**
   * {@inheritdoc
   */
  public function get($key) {
    if ($this->has($key)) {
      return $this->config->offsetGet($key);
    }
    throw new \Exception("The configuration for {$key} doesn't exist.");
  }

  /**
   * {@inheritdoc
   */
  public function has($key): bool {
    return $this->config->offsetExists($key);
  }

}