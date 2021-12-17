<?php


namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class PermissionIdentityExpansion.
 * Class to hold the Permission Identity for expansion.
 *  identityType (User, Group, Virtual Group ==> PermissionIdentityType),
 *  identity (for example: *@* or peter@coveo.com),
 *  securityProvider (for example: Confluence Provider).
 */
class PermissionIdentityExpansion {

  /**
   * The identityType/Type (User, Group or Virtual Group).
   *
   * @var PermissionIdentityType
   */
  public $type = '';

  /**
   * The associated identity provider identifier. By default, if no securityProvider is specified, the identity will be associated the default
   * securityProvider/Provider defined in the configuration.
   *
   * @var string
   */
  public $provider = '';

  /**
   * The identity/name provided by the identity provider to identify the permission identity.
   *
   * @var string
   */
  public $name = '';

  /**
   * The additional information is a collection of key value pairs that can be used to uniquely identify the permission identity.
   *
   * @var array
   */
  public $additionalInfo = array();

 /**
  * PermissionIdentity constructor.
  *
  * @param string $p_IdentityType
  *   PermissionIdentityType.
  * @param string $p_SecurityProvider
  *   Security Provider name.
  * @param string $p_Identity
  *   Identity to add.
  * @param array|null $p_AdditionalInfo
  *   AdditionalInfo dict {} to add.
  */
  function __construct( string $p_IdentityType, string $p_SecurityProvider, string $p_Identity, array $p_AdditionalInfo = NULL) {
    $this->name = $p_Identity;
    $this->provider = $p_SecurityProvider;
    $this->type = $p_IdentityType;
    $this->additionalInfo = $p_AdditionalInfo ?? array();
  }

}
