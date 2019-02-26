<?php

namespace Drupal\salesforce_rest\Services;

trait RestClientAwareTrait {

  /**
   * @var \Drupal\salesforce_rest\Services\RestClient
   */
  protected $restClient;

  /**
   * @param \Drupal\salesforce_rest\Services\RestClient $restClient
   */
  public function setRestClient(RestClient $restClient) {
    $this->restClient = $restClient;
  }

  /**
   * @return \Drupal\salesforce_rest\Services\RestClient
   */
  public function getRestClient(): RestClient {
    return $this->restClient;
  }

}