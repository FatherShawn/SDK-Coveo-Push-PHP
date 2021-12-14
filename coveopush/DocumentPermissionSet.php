<?php

namespace Coveo\Search\SDK\SDKPushPHP;

use Coveo\Search\SDK\SDKPushPHP\Enum;
use Coveo\Search\SDK\SDKPushPHP\Constants;
use Coveo\Search\Api\Service\LoggerInterface;

/**
 * Class DocumentPermissionSet. Class to hold one Permission Set.
 */
class DocumentPermissionSet {

  /**
   * The name of the permission set.
   *
   * @var string
   */
  public $Name = '';

  /**
   * Whether to allow anonymous access to the document or not.
   *
   * @var bool
   */
  public $AllowAnonymous = FALSE;

  /**
   * The allowed permissions. List of PermissionIdentity.
   *
   * @var array
   */
  public $AllowedPermissions = array();

  /**
   * The denied permissions. List of PermissionIdentity.
   *
   * @var array
   */
  public $DeniedPermissions = array();

  /**
   * Default constructor used by the deserialization.
   *
   * @param string $p_Name
   */
  function __construct(string $p_Name) {
    $this->Name = $p_Name;
    $this->AllowAnonymous = FALSE;
    $this->AllowedPermissions = array();
    $this->DeniedPermissions = array();
  }

  /**
   * Add a list of PermissionIdentities to the AllowedPermissions.
   *
   * @param array $p_PermissionIdentities
   *   List of PermissionIdentity.
   */
  function AddAllowedPermissions($p_PermissionIdentities) {
    //Debug('AddAllowedPermissions');
    // Check if correct
    if ($p_PermissionIdentities == NULL || empty($p_PermissionIdentities)) {
      return;
    }

    if (!is_array($p_PermissionIdentities)) {
      $p_PermissionIdentities = array($p_PermissionIdentities);
    }
    if (!is_a($p_PermissionIdentities[0], 'Coveo\\SDK\\SDKPushPHP\\PermissionIdentity')) {
      //Error( "AddAllowedPermissions: value is not of type PermissionIdentity");
      return;
    }
    $this->AllowedPermissions = array_merge($this->AllowedPermissions, $p_PermissionIdentities);
  }

  /**
   * Add a list of PermissionIdentities to the DeniedPermissions.
   *
   * @param array[\Coveo\SDK\SDKPushPHP\PermissionIdentity] $p_PermissionIdentities
   *   List of p_PermissionIdentities.
   */
  function AddDeniedPermissions($p_PermissionIdentities) {
    //Debug('AddDeniedPermissions');
    // Check if correct
    if ($p_PermissionIdentities == NULL || empty($p_PermissionIdentities)) {
      return;
    }

    if (!is_array($p_PermissionIdentities)) {
      $p_PermissionIdentities = array($p_PermissionIdentities);
    }
    if (!is_a($p_PermissionIdentities[0], 'Coveo\\SDK\\SDKPushPHP\\PermissionIdentity')) {
      //Error( "AddDeniedPermissions: value is not of type PermissionIdentity");
      return;
    }
    $this->DeniedPermissions = array_merge($this->DeniedPermissions, $p_PermissionIdentities);
  }

}
