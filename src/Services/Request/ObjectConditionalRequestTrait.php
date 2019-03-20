<?php

namespace Drupal\salesforce_rest\Services\Request;

trait ObjectConditionalRequestTrait {

  /**
   * @var array
   */
  private $validConditionalOperators = [
    'AND',
    'OR'
  ];

  /**
   * @var array
   */
  protected $objectConditions;

  /**
   * @return array
   */
  public function getObjectConditions(): array {
    return $this->objectConditions ?? [];
  }

  /**
   * @param array $objectConditions
   *
   * @return \Drupal\salesforce_rest\Services\Request\ObjectConditionalRequestTrait
   * @throws \Exception
   */
  public function setObjectConditions(array $objectConditions): self {
    if (empty(array_intersect(
      array_keys($objectConditions),
      $this->validConditionalOperators
    ))) {
      throw new \Exception(
        "The conditional operators must be " . implode(' OR ', $this->validConditionalOperators)
      );
    }
    $this->objectConditions = $objectConditions;
    return $this;
  }

}