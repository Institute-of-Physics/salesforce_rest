<?php

namespace Drupal\salesforce_rest\Services\Query;

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
    if (empty($this->getObjectType()) ||
      empty($this->getObjectId())) {
      throw new \Exception("The object type and/or Id must be set.");
    }
    return "/sobjects/{$this->getObjectType()}/{$this->getObjectId()}";
  }

  /**
   * {@inheritdoc}
   */
  public function getParams(): array {
    if (empty($this->getObjectFields())) {
      throw new \Exception("The object's fields must be set.");
    }
    return [
      'fields' => implode(',', $this->getObjectFields()),
    ];
  }

}