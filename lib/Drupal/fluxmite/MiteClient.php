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
class MiteGraphClient extends Client {

  /**
   * {@inheritdoc}
   */
  public static function factory($config = array()) {
    $required = array('base_url','subdomain', 'access_token');
    $config = Collection::fromConfig($config, array(), $required);
    $client = new static('', $config);

    // Attach a service description to the client
    $description = ServiceDescription::factory(__DIR__ . '/Mite.json');
    $client->setDescription($description);

    // Add the OAuth plugin as an event subscriber using the credentials given
    // in the configuration array.
    $client->addSubscriber(new OauthPlugin(array(
      'client_id' => $config->get('subdomain'),
      'client_secret' => $config->get('access_token'),
      'access_token' => $config->get('access_token'),
      'appsecret_proof' => $config->get('subdomain'),
    )));

    kpr($client);

    return $client;
  }

}
