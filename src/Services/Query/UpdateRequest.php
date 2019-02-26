<?php

namespace Drupal\salesforce_rest\Services\Query;

final class UpdateRequest extends RequestAbstract {

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
    return 'PATCH';
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
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getBody(): array {
    if (empty($this->getObjectFieldValues())) {
      throw new \Exception("The object's field values must be set.");
    }
    return $this->getObjectFieldValues();
  }

}