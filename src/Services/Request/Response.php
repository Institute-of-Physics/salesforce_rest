<?php

namespace Drupal\salesforce_rest\Services\Request;

use \Drupal\salesforce_rest\Contracts\ImmutableInterface;
use \Symfony\Component\PropertyAccess\PropertyAccess;

final class Response implements ImmutableInterface {

  /**
   * @var string
   */
  const INVALID_SESSION_ERROR_CODE = 'INVALID_SESSION_ID';

  /**
   * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
   */
  private $propertyAccessor;

  /**
   * @var \stdClass
   */
  private $response;

  /**
   * @param string $response
   */
  public function __construct(string $response) {
    $this->propertyAccessor =
      PropertyAccess::createPropertyAccessorBuilder()
        ->disableMagicCall()
        ->getPropertyAccessor();
    $this->response = json_decode($response);
  }

  /**
   * @return \stdClass
   */
  public function __toString():string {
    return json_encode($this->response) ?? '';
  }

  /**
   * @param $key
   *
   * @return mixed
   * @throws \Exception
   */
  public function get($key) {
    if ($this->has($key)) {
      return $this->propertyAccessor->getValue($this->response, $key);
    }
    throw new \Exception("The value for {$key} doesn't exist.");
  }

  /**
   * @param $key
   *
   * @return bool
   */
  public function has($key): bool {
    return $this->propertyAccessor->isReadable($this->response, $key);
  }

}