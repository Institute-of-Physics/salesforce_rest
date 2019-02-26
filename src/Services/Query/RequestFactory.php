<?php

namespace Drupal\salesforce_rest\Services\Query;

use \Drupal\salesforce_rest\Services\RestClient;

final class RequestFactory {

  /**
   * @var \Drupal\salesforce_rest\Services\RestClient
   */
  private $restClient;

  /**
   * @param \Drupal\salesforce_rest\Services\RestClient $restClient
   */
  public function __construct(RestClient $restClient) {
    $this->restClient = $restClient;
  }

  /**
   * @return \Drupal\salesforce_rest\Services\Query\FuzzyRequest
   */
  public function createFuzzyRequest(): FuzzyRequest {
    return FuzzyRequest::create($this->restClient);
  }

  /**
   * @return \Drupal\salesforce_rest\Services\Query\SelectRequest
   */
  public function createSelectRequest(): SelectRequest {
    return NativeRequest::create($this->restClient);
  }

  /**
   * @return \Drupal\salesforce_rest\Services\Query\UpdateRequest
   */
  public function createUpdateRequest(): UpdateRequest {
    return UpdateRequest::create($this->restClient);
  }

}