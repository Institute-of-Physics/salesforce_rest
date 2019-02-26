<?php

namespace Drupal\salesforce_rest\Services\Query;

trait ObjectReadRequestTrait {

  /**
   * @var array
   */
  protected $objectFields;

  /**
   * @return array
   */
  public function getObjectFields(): array {
    return $this->objectFields;
  }

  /**
   * @param array $objectFields
   *
   * @return \Drupal\salesforce_rest\Services\Query\ObjectReadRequestTrait
   */
  public function setObjectFields(array $objectFields): self {
    $this->objectFields = $objectFields;
    return $this;
  }

}