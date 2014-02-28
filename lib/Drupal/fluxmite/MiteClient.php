<?php

/**
 * @file
 * Contains MiteUserClient.
 */

namespace Drupal\fluxmite;

use Drupal\fluxmite\Guzzle\MiteAuthenticationPlugin;
use Drupal\fluxservice\ServiceClientInterface;
use Guzzle\Common\Collection;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;

/**
 * Guzzle driven service client for the Twitter API.
 */
class MiteClient extends Client {

  /**
   * {@inheritdoc}
   */
  public static function factory($config = array()) {
    $required = array('base_url', 'access_token');

    $config = Collection::fromConfig($config, array(), $required);
    $client = new static($config->get('base_url'), $config);

    // Attach a service description to the client
    $description = ServiceDescription::factory(__DIR__ . '/Mite.json');
    $client->setDescription($description);

    return $client;
  }

}
