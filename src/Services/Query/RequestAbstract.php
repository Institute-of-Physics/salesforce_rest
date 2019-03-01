<?php

namespace Drupal\salesforce_rest\Services\Query;

use \Drupal\salesforce_rest\Services\RestClient;

abstract class RequestAbstract {

  /**
   * @var \Drupal\cb_salesforce\Services\RestClient
   */
  protected $restClient;

  /**
   * @param \Drupal\salesforce_rest\Services\RestClient $restClient
   */
  private function __construct(RestClient $restClient) {
    $this->restClient = $restClient;
  }

  /**
   * @param \Drupal\salesforce_rest\Services\RestClient $restClient
   *
   * @return \Drupal\salesforce_rest\Services\Query\RequestAbstract
   */
  public static function create(RestClient $restClient): RequestAbstract {
    return new static($restClient);
  }

  /**
   * @return RequestResponse
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function execute(): RequestResponse {
    $response = $this->restClient->request($this);
    return new RequestResponse($response);
  }

  /**
   * @return string
   */
  abstract public function getMethod(): string;

  /**
   * @return string
   */
  abstract public function getUri(): string;

}