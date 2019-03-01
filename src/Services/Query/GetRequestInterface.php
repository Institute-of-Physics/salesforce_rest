<?php

namespace Drupal\salesforce_rest\Services\Query;

interface GetRequestInterface {

  /**
   * @return array
   */
  public function getParams(): array;
  
}