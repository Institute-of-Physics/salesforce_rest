<?php

namespace Drupal\salesforce_rest\Services\Request;

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
   * @return \Drupal\salesforce_rest\Services\Request\RequestAbstract
   */
  public static function create(RestClient $restClient): RequestAbstract {
    return new static($restClient);
  }

  /**
   * @return \Drupal\salesforce_rest\Services\Request\Response
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function execute(): Response {
    $response = $this->restClient->request($this);
    return new Response($response);
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