<?php

/**
 * @file
 * Contains MiteAccount.
 */

namespace Drupal\fluxmite\Plugin\Service;

use Drupal\fluxmite\MiteClient;
use Drupal\fluxservice\Plugin\Entity\Account;
use Guzzle\Http\Client;
use Guzzle\Http\Url;
use Guzzle\Service\Builder\ServiceBuilder;

/**
 * Account implementation for Mite.
 */
class MiteAccount extends Account implements MiteAccountInterface {

  /**
   * Defines the plugin.
   */
  public static function getInfo() {
    return array(
      'name' => 'fluxmite',
      'label' => t('Mite account'),
      'description' => t('Provides Mite integration for fluxkraft.'),
      'class' => '\Drupal\fluxmite\Plugin\Service\MiteAccount',
      'service' => 'fluxmite',
    );
  }

    /**
   * {@inheritdoc}
   */
  public function getDefaultSettings() {
    return parent::getDefaultSettings() + array(
      'access_token' => '',
    );
  }


  /**
   * The service base url.
   *
   * @var string
   */
  protected $serviceUrl = 'mite.yo.lk';

  public function getAccessToken(){
    return $this->data->get('access_token');
  }

  public function client(){
    $service = $this->getService();
    return MiteClient::factory(array(
      'base_url' => 'https://'.$service->getSubdomain().'.'.$serviceUrl,
      'subdomain' => $service->getSubdomain(),
      'access_token' => $this->getAccessToken(),
    ));
  }

    /**
   * {@inheritdoc}
   */
  public function settingsForm(array &$form_state) {
    $form['authentication'] =  array(
      '#type' => 'fieldset',
      '#title' => t('Authentication'),
      '#description' => t('The API key is needed for authenticating your account. It is found at Account/My User. Just "Allow API access", "Display the API key" and paste it here.'),
      // Avoid the data being nested below 'rules' or falling out of 'data'.
      '#parents' => array('data'),
    );
    $form['authentication']['access_token'] = array(
      '#type' => 'textfield',
      '#title' => t('API key'),
      '#default_value' => $this->getAccessToken(),
      '#description' => t('The API key of this account'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function getAccountForOAuthCallback($key, $plugin) {
    $store = fluxservice_tempstore("fluxservice.account.{$plugin}");
    return $store->getIfOwner($key);
  }

  /**
   * {@inheritdoc}
   */
  public function accessOAuthCallback(){

  }

  /**
   * {@inheritdoc}
   */
  public function processOAuthCallback(){

  }
}