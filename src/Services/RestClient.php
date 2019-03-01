<?php

namespace Drupal\salesforce_rest\Services;

use \Drupal\Core\Cache\CacheBackendInterface;
use \Drupal\Core\Logger\LoggerChannelInterface;
use \Drupal\salesforce_rest\Config\Config;
use \Drupal\salesforce_rest\Services\Request\{
  RequestAbstract,
  Contracts\GetRequestInterface,
  Contracts\PostRequestInterface,
};
use \Drupal\salesforce_rest\Session\AccessToken;
use \Symfony\Component\HttpKernel\Exception\HttpException;
use \Symfony\Component\HttpFoundation\Response;
use \GuzzleHttp\Client;

class RestClient {

  /**
   * @var string
   */
  const API_ENDPOINT = '/services/data/v{api_version}';

  /**
   * @var \GuzzleHttp\Client
   */
  private $client;

  /**
   * @var \Drupal\cb_salesforce\Config\ImmutableInterface
   */
  private $config;

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  private $cache;

  /**
   * @var \Drupal\cb_salesforce\Session\AccessToken
   */
  private $accessToken;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  private $logger;

  /**
   * @param \Drupal\salesforce_rest\Config\Config $config
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function __construct(
    Config $config,
    CacheBackendInterface $cache,
    LoggerChannelInterface $logger
  ) {
    $this->config = $config->getConfig();
    $this->cache = $cache;
    $this->logger = $logger;

    if ($accessToken = $this->cache->get('access_token')) {
      $this->accessToken = $accessToken->data;
    } else {
      $this->accessToken = $this->getAccessToken();
      $this->cache->set('access_token', $this->accessToken);
    }
  }

  /**
   * @return \Drupal\salesforce_rest\Session\AccessToken
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  private function getAccessToken(): AccessToken {
    try {
      $requestHeaders = [
        'Content-Type' => 'application/x-www-form-urlencoded',
      ];
      $requestBody = [
        'grant_type' => 'password',
        'client_id' => $this->config->get('oauth_client_id'),
        'client_secret' => $this->config->get('oauth_client_secret'),
        'username' => $this->config->get('oauth_username'),
        'password' => $this->config->get('oauth_password') . $this->config->get('oauth_token'),
      ];
      $client = new Client([
        'base_uri' => $this->config->get('oauth_base_uri'),
        'headers' => $requestHeaders,
        'form_params' => $requestBody,
      ]);
      $response = $client->request(
        'POST',
        '/services/oauth2/token'
      );
      if (Response::HTTP_OK !== $response->getStatusCode()) {
        throw new HttpException($response->getStatusCode(), $response->getReasonPhrase());
      }
      return new AccessToken(
        $response->getBody()->getContents()
      );
    } catch (\Exception $e) {
      $this->logger
        ->critical($e->getMessage());
      throw $e;
    }
  }

  /**
   * @return string
   */
  private function getApiEndpoint(): string {
    return str_replace(
      '{api_version}',
      $this->config->get('api_version'),
      self::API_ENDPOINT
    );
  }

  /**
   * @param \Drupal\salesforce_rest\Services\Request\RequestAbstract $request
   *
   * @return string
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function request(RequestAbstract $request): string {
    try {
      $requestData = [];
      if ($request instanceof GetRequestInterface &&
        $request->hasParams()) {
        $requestData['query'] = $request->getParams();
      } else if ($request instanceof PostRequestInterface &&
        $request->hasBody()) {
        $requestData['json'] = $request->getBody();
      }
      $response = $this->getClient()
        ->request(
          $request->getMethod(),
          $this->getApiEndpoint() . $request->getUri(),
          $requestData
        );
      if (!in_array($response->getStatusCode(), [
        Response::HTTP_OK,
        Response::HTTP_CREATED,
        Response::HTTP_NO_CONTENT
      ])) {
        throw new HttpException($response->getStatusCode(), $response->getReasonPhrase());
      }
      return $response->getBody()->getContents();
    } catch (\Exception $e) {
      $this->logger
        ->critical($e->getMessage());
      throw $e;
    }
  }

  /**
   * @return \GuzzleHttp\Client
   */
  private function getClient() {
    if ($this->client) {
      return $this->client;
    }
    $requestHeaders = [
      'Authorization' => implode(' ', [
        $this->accessToken->getTokenType(),
        $this->accessToken->getAccessToken()
      ]),
      'Content-Type' => 'application/json',
    ];
    return $this->client = new Client([
      'base_uri' => $this->accessToken->getInstanceUrl(),
      'headers' => $requestHeaders
    ]);
  }

}