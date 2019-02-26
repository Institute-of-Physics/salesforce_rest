<?php

namespace Drupal\salesforce_rest\Config;

use \Drupal\salesforce_rest\Contracts\ImmutableInterface;
use \Drupal\Core\Config\ConfigFactoryInterface;
use \Drupal\Core\Logger\LoggerChannelInterface;

final class Config {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * @var mixed
   */
  protected $config;

  /**
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   */
  public function __construct(
    ConfigFactoryInterface $configFactory,
    LoggerChannelInterface $logger
  ) {
    $this->configFactory = $configFactory;
    $this->logger = $logger;
  }

  /**
   * @param string $namespace
   * @param string $path
   *
   * @return mixed
   */
  public function createConfig(string $namespace, string $path) {
    try {
      $config =
        $this->configFactory
          ->get($namespace)
          ->get($path);
      if (is_array($config)) {
        $this->config = new ArrayConfig($config);
      }
    } catch (\Exception $e) {
      $this->logger
        ->critical($e->getMessage());
    }
  }

  /**
   * @return mixed
   */
  public function getConfig(): ImmutableInterface {
    return $this->config;
  }

}