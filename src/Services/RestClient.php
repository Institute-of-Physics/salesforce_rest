<?php

namespace Drupal\salesforce_rest\Services;

use \Drupal\Core\Cache\CacheBackendInterface;
use \Drupal\Core\Logger\LoggerChannelInterface;
use \Drupal\salesforce_rest\Config\Config;
use \Drupal\salesforce_rest\Services\Request\{
  RequestAbstract,
  Response,
  Contracts\GetRequestInterface,
  Contracts\PostRequestInterface,
};
use \Drupal\salesforce_rest\Session\AccessToken;
use \Symfony\Component\HttpKernel\Exception\HttpException;
use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use \GuzzleHttp\Exception\ClientException;
use \Psr\Http\Message\ResponseInterface;
use \GuzzleHttp\Client;
use \GuzzleHttp\ClientInterface;

class RestClient {

  /**
   * @var string
   */
  const API_ENDPOINT = '/services/data/v{api_version}';

  /**
   * @var \GuzzleHttp\ClientInterface
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
    $this->accessToken = $this->getAccessToken();
  }

  /**
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  private function regenerateAccessToken(): void {
    $this->cache->delete('access_token');
    $this->accessToken = $this->getAccessToken();
  }

  /**
   * @return \Drupal\salesforce_rest\Session\AccessToken
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  private function getAccessToken(): AccessToken {
    try {
      if ($accessToken = $this->cache->get('access_token')) {
        return $accessToken->data;
      }
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
      if (SymfonyResponse::HTTP_OK !== $response->getStatusCode()) {
        throw new HttpException($response->getStatusCode(), $response->getReasonPhrase());
      }
      $accessToken = new AccessToken($response->getBody()->getContents());
      $this->cache->set('access_token', $accessToken);
      return $accessToken;
    } catch (\Exception $e) {
      $this->logger
        ->critical($e->getMessage());
      throw $e;
    }
  }

  /**
   * @param \Psr\Http\Message\ResponseInterface $response
   *
   * @return bool
   * @throws \Exception
   */
  private function hasAccessTokenExpired(ResponseInterface $response): bool {
    if (SymfonyResponse::HTTP_UNAUTHORIZED === $response->getStatusCode()) {
      $response = new Response($response->getBody()->getContents());
      if ($response->has('[0].errorCode') &&
        Response::INVALID_SESSION_ERROR_CODE === $response->get('[0].errorCode')) {
        return TRUE;
      }
    }
    return FALSE;
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
   * @return \Drupal\salesforce_rest\Services\Request\Response
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function request(RequestAbstract $request): Response {
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
        SymfonyResponse::HTTP_OK,
        SymfonyResponse::HTTP_CREATED,
        SymfonyResponse::HTTP_NO_CONTENT
      ])) {
        throw new HttpException($response->getStatusCode(), $response->getReasonPhrase());
      }
      return new Response($response->getBody()->getContents());
    } catch (\Exception $e) {
      if ($e instanceof ClientException) {
        if ($this->hasAccessTokenExpired($e->getResponse())) {
          $this->regenerateAccessToken();
          return $this->retryRequest($request);
        }
      }
      $this->logger
        ->critical($e->getMessage());
      throw $e;
    }
  }

  /**
   * @param \Drupal\salesforce_rest\Services\Request\RequestAbstract $request
   *
   * @return \Drupal\salesforce_rest\Services\Request\Response
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  private function retryRequest(RequestAbstract $request): Response {
    static $retries = 0;
    $retries++;
    if ($retries > 3) {
      throw new \Exception(
        "A request cannot be retried more than three times."
      );
    }
    return $this->request($request);
  }

  /**
   * @return \GuzzleHttp\ClientInterface
   */
  private function getClient(): ClientInterface {
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