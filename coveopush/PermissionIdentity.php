<?php

namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class PermissionIdentity.
 * Class to hold the Permission Identity.
 * identityType (User, Group, Virtual Group ==> PermissionIdentityType),
 * identity (for example: *@* or peter@coveo.com),
 * SecurityProvider (for example: Confluence Provider).
 */
class PermissionIdentity {

  /**
   * The identityType (User, Group or Virtual Group). PermissionIdentityType.
   *
   * @var string
   */
  public $identityType = '';

  /**
   * The associated identity provider identifier.By default, if no securityProvider is specified, the identity will be associated the default securityProvider defined in the configuration.
   *
   * @var string
   */
  public $securityProvider = '';

  /**
   * The identity provided by the identity provider to identify the permission identity.
   *
   * @var string
   */
  public $identity = '';

  /**
   * The additional information is a collection of key value pairs that can be used to uniquely identify the permission identity.
   *
   * @var array
   */
  public $AdditionalInfo = array();

  /**
   * PermissionIdentity constructor.
   *
   * @param string $p_IdentityType
   *   PermissionIdentityType.
   * @param string $p_SecurityProvider
   *   Security Provider name.
   * @param string $p_Identity
   *   Identity to add.
   * @param array|NULL $p_AdditionalInfo
   *   AdditionalInfo dict {} to add.
   */
  function __construct( string $p_IdentityType, string $p_SecurityProvider, string $p_Identity, array $p_AdditionalInfo = NULL) {
    $this->identity = $p_Identity;
    $this->securityProvider = $p_SecurityProvider;
    $this->identityType = $p_IdentityType;
    $this->AdditionalInfo = $p_AdditionalInfo ?? array();
  }

}
