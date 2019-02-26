<?php

namespace Drupal\salesforce_rest\Services\Query;

trait ObjectRequestTrait {

  /**
   * @var string
   */
  protected $objectType;

  /**
   * @var string
   */
  protected $objectId;

  /**
   * @return string
   */
  public function getObjectType(): string {
    return $this->objectType;
  }

  /**
   * @param string $objectType
   *
   * @return \Drupal\salesforce_rest\Services\Query\ObjectRequestTrait
   */
  public function setObjectType(string $objectType): self {
    $this->objectType = $objectType;
    return $this;
  }

  /**
   * @return string
   */
  public function getObjectId(): string {
    return $this->objectId;
  }

  /**
   * @param string $objectId
   *
   * @return \Drupal\salesforce_rest\Services\Query\ObjectRequestTrait
   */
  public function setObjectId(string $objectId): self {
    $this->objectId = $objectId;
    return $this;
  }

}