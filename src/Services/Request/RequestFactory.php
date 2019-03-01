<?php

namespace Drupal\salesforce_rest\Services\Request;

use \Drupal\salesforce_rest\Services\RestClient;
use \Drupal\salesforce_rest\Services\Request\Types\{
  FuzzyRequest,
  SelectRequest,
  UpdateRequest,
};

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
   * @return \Drupal\salesforce_rest\Services\Request\Types\FuzzyRequest
   */
  public function createFuzzyRequest(): FuzzyRequest {
    return FuzzyRequest::create($this->restClient);
  }

  /**
   * @return \Drupal\salesforce_rest\Services\Request\Types\SelectRequest
   */
  public function createSelectRequest(): SelectRequest {
    return SelectRequest::create($this->restClient);
  }

  /**
   * @return \Drupal\salesforce_rest\Services\Request\Types\UpdateRequest
   */
  public function createUpdateRequest(): UpdateRequest {
    return UpdateRequest::create($this->restClient);
  }

}