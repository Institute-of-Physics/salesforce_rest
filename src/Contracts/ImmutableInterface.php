<?php

namespace Drupal\salesforce_rest\Contracts;

interface ImmutableInterface {

  /**
   * @param $key
   *
   * @return mixed
   */
  public function get($key);

  /**
   * @param $key
   *
   * @return bool
   */
  public function has($key): bool;

}