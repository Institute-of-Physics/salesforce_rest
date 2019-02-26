# Salesforce REST

## Description
A Drupal module that provides RESTFUL querying capabilities to Salesforce.

## Configuration

This module uses Drupal's configuration override system, the configuration is not exported and instead set at runtime. The configuration schema that is provided with this module is only intended as documentation.

The configuration must be included within the settings.php and declared as any other configuration override:

```
$config['rest_client']['oauth_base_uri'] = 'http://test.salesforce.com'
$config['rest_client']['oauth_username'] = ''
$config['rest_client']['oauth_password'] = ''
$config['rest_client']['oauth_client_id'] = ''
$config['rest_client']['oauth_client_secret'] = ''
$config['rest_client']['oauth_token'] = ''
$config['rest_client']['api_version'] = ''
```

**Note:** The example illustrates the complete list of configuration.

## Usage

```
$requestFactory = \Drupal::service('salesforce_rest.services.query.request_factory');
$fuzzyRequest = $requestFactory->createFuzzyRequest();

$fuzzyRequest->setType('User');
$fuzzyRequest->setFields(['Id']);

$requestResponse = $fuzzyRequest->execute();

```