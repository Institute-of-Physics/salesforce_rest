<?php

namespace Drupal\salesforce_rest\Services\Request\Contracts;

interface GetRequestInterface {

  /**
   * @return array
   */
  public function getParams(): array;

  /**
   * @return bool
   */
  public function hasParams(): bool;
  
}