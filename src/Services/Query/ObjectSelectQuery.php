<?php

namespace Drupal\salesforce_rest\Services\Query;

final class ObjectSelectQuery {

  /**
   * @var string
   */
  private $type;

  /**
   * @var array
   */
  private $fields = ['Id'];

  /**
   * @var array
   */
  private $where = [];

  /**
   * @param string $type
   */
  public function __construct(string $type) {
    $this->type = $type;
  }

  /**
   * @param array $fields
   */
  public function fields(array $fields) {
    $this->fields = $fields;
  }

  /**
   * @param string $field
   * @param string $operator
   * @param $value mixed
   * @param string $delimiter
   */
  public function where(
    string $field,
    string $operator,
    $value,
    string $delimiter = 'AND') {
    $this->where[$delimiter][] = [
      $field,
      $operator,
      $value,
    ];
  }

  /**
   * @return string
   */
  public function __toString(): string {
    $query[] = "SELECT " . implode(', ', $this->fields);
    $query[] = "FROM " . $this->type;
    if (!empty($this->where)) {
      $query[] = "WHERE";
      foreach ($this->where as $delimiter => $conditions) {
        $where = [];
        foreach ($conditions as $condition) {
          $field = "{$condition[0]}";
          $operator = "{$condition[1]}";
          $value = (is_string($condition[2]) ? "'{$condition[2]}'" : "{$condition[2]}");
          $where[] = "{$field} {$operator} {$value}";
        }
        $query[] = implode(" {$delimiter} ", $where);
      }
    }
    return implode(' ', $query);
  }

}