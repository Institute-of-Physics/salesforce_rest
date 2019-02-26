<?php

namespace Drupal\salesforce_rest\Services\Query;

final class FuzzyRequest extends RequestAbstract {

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
    return 'GET';
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
    if (empty($this->getObjectType()) ||
      empty($this->getObjectFields())) {
      throw new \Exception("The object type and/or fields must be set.");
    }
    $query[] = "SELECT " . implode(', ', $this->getObjectFields());
    $query[] = "FROM " . $this->getObjectType();
    if ($groupedConditions = $this->getObjectConditions()) {
      $query[] = "WHERE";
      foreach ($groupedConditions as $logicalOperator => $conditions) {
        $where = [];
        foreach ($conditions as $conditionOperand) {
          $field = "{$conditionOperand[0]}";
          $comparisonOperator = "{$conditionOperand[1]}";
          $value = (is_string($conditionOperand[2]) ? "'{$conditionOperand[2]}'" : "{$conditionOperand[2]}");
          $where[] = "{$field} {$comparisonOperator} {$value}";
        }
        $query[] = implode(" {$logicalOperator} ", $where);
      }
    }
    return [
      'q' => implode(' ', $query)
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getBody(): array {
    return [];
  }

}