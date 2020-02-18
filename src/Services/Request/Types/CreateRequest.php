<?php

namespace Drupal\salesforce_rest\Services\Request\Types;

use \Drupal\salesforce_rest\Services\Request\Contracts\PostRequestInterface;
use \Drupal\salesforce_rest\Services\Request\{
  RequestAbstract,
  ObjectRequestTrait,
  ObjectWriteRequestTrait,
};
use \Symfony\Component\HttpFoundation\Request;

final class CreateRequest extends RequestAbstract implements PostRequestInterface {

  use ObjectRequestTrait {
    ObjectRequestTrait::setObjectType as setType;
  }
  use ObjectWriteRequestTrait {
    ObjectWriteRequestTrait::setObjectFieldValues as setFieldValues;
  }

  /**
   * {@inheritdoc}
   */
  public function getMethod(): string {
    return Request::METHOD_POST;
  }

  /**
   * {@inheritdoc}
   */
  public function getUri(): string {
    return "/sobjects/{$this->getObjectType()}";
  }

  /**
   * {@inheritdoc}
   */
  public function getBody(): array {
    return $this->getObjectFieldValues();
  }

  /**
   * @return bool
   */
  public function hasBody(): bool {
    if (!empty($this->getObjectFieldValues())) {
      return TRUE;
    }
    return FALSE;
  }

}