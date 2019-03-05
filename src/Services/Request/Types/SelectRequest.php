<?php

namespace Drupal\salesforce_rest\Services\Request\Types;

use \Drupal\salesforce_rest\Services\Request\Contracts\GetRequestInterface;
use \Drupal\salesforce_rest\Services\Request\{
  RequestAbstract,
  ObjectRequestTrait,
  ObjectReadRequestTrait,
};
use \Symfony\Component\HttpFoundation\Request;

final class SelectRequest extends RequestAbstract implements GetRequestInterface {

  use ObjectRequestTrait {
    ObjectRequestTrait::setObjectId as setId;
    ObjectRequestTrait::setObjectType as setType;
  }
  use ObjectReadRequestTrait {
    ObjectReadRequestTrait::setObjectFields as setFields;
  }

  /**
   * {@inheritdoc}
   */
  public function getMethod(): string {
    return Request::METHOD_GET;
  }

  /**
   * {@inheritdoc}
   */
  public function getUri(): string {
    return "/sobjects/{$this->getObjectType()}/{$this->getObjectId()}";
  }

  /**
   * {@inheritdoc}
   */
  public function getParams(): array {
    return [
      'fields' => implode(',', $this->getObjectFields()),
    ];
  }

  /**
   * @return bool
   */
  public function hasParams(): bool {
    if (!empty($this->getObjectFields())) {
      return TRUE;
    }
    return FALSE;
  }

}