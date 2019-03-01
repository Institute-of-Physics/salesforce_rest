<?php

namespace Drupal\salesforce_rest\Services\Query;

interface PostRequestInterface {

  /**
   * @return array
   */
  public function getBody(): array;

}