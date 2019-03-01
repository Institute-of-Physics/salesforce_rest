<?php

namespace Drupal\salesforce_rest\Services\Request\Types;

use \Drupal\salesforce_rest\Services\Request\{
  RequestAbstract,
  ObjectRequestTrait,
  ObjectReadRequestTrait,
  ObjectConditionalRequestTrait,
};
use \Drupal\salesforce_rest\Services\Request\Contracts\GetRequestInterface;
use \Drupal\salesforce_rest\Services\Query\ObjectSelectQuery;
use \Symfony\Component\HttpFoundation\Request;

final class FuzzyRequest extends RequestAbstract implements GetRequestInterface {

  use ObjectRequestTrait {
    ObjectRequestTrait::setObjectId as setId;
    ObjectRequestTrait::setObjectType as setType;
  }
  use ObjectReadRequestTrait {
    ObjectReadRequestTrait::setObjectFields as setFields;
  }
  use ObjectConditionalRequestTrait {
    ObjectConditionalRequestTrait::setObjectConditions as setConditions;
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
    return '/query/';
  }

  /**
   * {@inheritdoc}
   */
  public function getParams(): array {
    $objectSelectQuery = new ObjectSelectQuery($this->getObjectType());
    $objectSelectQuery->fields($this->getObjectFields());
    foreach ($this->getObjectConditions() as $delimiter => $conditions) {
      foreach ($conditions as $where) {
        $where[] = $delimiter;
        $objectSelectQuery->where(...$where);
      }
    }
    return [
      'q' => $objectSelectQuery->__toString(),
    ];
  }

  /**
   * @return bool
   */
  public function hasParams(): bool {
    if (!empty($this->getObjectType()) &&
      !empty($this->getObjectFields())) {
      return TRUE;
    }
    return FALSE;
  }

}