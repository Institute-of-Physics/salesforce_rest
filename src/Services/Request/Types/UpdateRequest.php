<?php

namespace Drupal\salesforce_rest\Services\Request\Types;

use \Drupal\salesforce_rest\Services\Request\Contracts\PostRequestInterface;
use \Drupal\salesforce_rest\Services\Request\{
  RequestAbstract,
  ObjectRequestTrait,
  ObjectWriteRequestTrait,
};
use \Symfony\Component\HttpFoundation\Request;

final class UpdateRequest extends RequestAbstract implements PostRequestInterface {

  use ObjectRequestTrait {
    ObjectRequestTrait::setObjectId as setId;
    ObjectRequestTrait::setObjectType as setType;
  }
  use ObjectWriteRequestTrait {
    ObjectWriteRequestTrait::setObjectFieldValues as setFieldValues;
  }

  /**
   * {@inheritdoc}
   */
  public function getMethod(): string {
    return Request::METHOD_PATCH;
  }

  /**
   * {@inheritdoc}
   */
  public function getUri(): string {
    if (empty($this->getObjectType()) ||
      empty($this->getObjectId())) {
      throw new \Exception("The object type and/or Id must be set.");
    }
    return "/sobjects/{$this->getObjectType()}/{$this->getObjectId()}";
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