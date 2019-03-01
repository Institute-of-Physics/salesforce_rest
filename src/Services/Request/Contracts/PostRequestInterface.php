<?php

namespace Drupal\salesforce_rest\Services\Request\Contracts;

interface PostRequestInterface {

  /**
   * @return array
   */
  public function getBody(): array;

  /**
   * @return bool
   */
  public function hasBody(): bool;

}