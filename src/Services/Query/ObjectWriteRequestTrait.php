<?php

namespace Drupal\salesforce_rest\Services\Query;

trait ObjectWriteRequestTrait {

  /**
   * @var array
   */
  protected $objectFieldValues;

  /**
   * @return array
   */
  public function getObjectFieldValues(): array {
    return $this->objectFieldValues;
  }

  /**
   * @param array $objectFieldValues
   *
   * @return \Drupal\salesforce_rest\Services\Query\ObjectWriteRequestTrait
   */
  public function setObjectFieldValues(array $objectFieldValues): self {
    $this->objectFieldValues = $objectFieldValues;
    return $this;
  }

}