<?php

namespace Drupal\salesforce_rest\Session;

final class AccessToken {

  /**
   * @var string
   */
  private $accessToken;

  /**
   * @var string
   */
  private $instanceUrl;

  /**
   * @var string
   */
  private $tokenType;

  /**
   * @param string $accessToken
   *
   * @throws \Exception
   */
  public function __construct(string $accessToken) {
    if (!$accessToken = json_decode($accessToken)) {
      throw new \Exception("The access token cannot be decoded.");
    }
    $this->setAccessToken($accessToken->access_token);
    $this->setInstanceUrl($accessToken->instance_url);
    $this->setTokenType($accessToken->token_type);
  }

  /**
   * @param string $accessToken
   */
  private function setAccessToken(string $accessToken) {
    $this->accessToken = $accessToken;
  }

  /**
   * @return string
   */
  public function getAccessToken(): string {
    return $this->accessToken;
  }

  /**
   * @param string $instanceUrl
   */
  private function setInstanceUrl(string $instanceUrl) {
    $this->instanceUrl = $instanceUrl;
  }

  /**
   * @return string
   */
  public function getInstanceUrl(): string {
    return $this->instanceUrl;
  }

  /**
   * @param string $tokenType
   */
  private function setTokenType(string $tokenType) {
    $this->tokenType = $tokenType;
  }

  /**
   * @return string
   */
  public function getTokenType(): string {
    return $this->tokenType;
  }
}