<?php

namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class to hold all associated Permission information for one Identity.
 */
class PermissionIdentityBody {

  /**
   * The identity. The identity is represented by a Name, a Type (User, Group or Virtual Group) and its Addtionnal Info). PermissionIdentity.
   *
   * @var string
   */
  public $identity = '';

  /**
   * The mappings of a user. List of PermissionIdentityExpansion.
   * Link different user identities in different systems that represent the same person.
   * For example:
   *    - corp\myuser (Active Directory)
   *    - myuser@myenterprise.com (Email)
   *
   * @var array[PermissionIdentityExpansion]
   */
  public $mappings = array();

  /**
   * The members of a group or a virtual group (membership). List of PermissionIdentityExpansion
   *
   * @var array
   */
  public $members = array();

  /**
   * The well-knowns. List of PermissionIdentityExpansion.
   * Well-known is a group that identifies generic users or generic groups.
   * For example, in the Active Directory:
   *    - Everyone: automatically includes everyone who uses the computer, even anonymous guests.
   *    - Anonymous: automatically includes all users that have logged on anonymously.
   *
   * @var array[PermissionIdentityExpansion]
   */
  public $wellKnowns = array();

  /**
   * Default constructor used by the deserialization.
   *
   * @param PermissionIdentityExpansion $p_Identity
   *   Identity name.
   */
  function __construct(PermissionIdentityExpansion $p_Identity) {
    if (!is_a($p_Identity, 'Coveo\\Search\\SDK\\SDKPushPHP\\PermissionIdentityExpansion')) {
      //Error("PermissionIdentityBody constructor: value is not of type PermissionIdentityExpansion");
      return;
    }

    $this->identity = $p_Identity;
    $this->mappings = array();
    $this->members = array();
    $this->wellKnowns = array();
  }

  /**
   * Add a PermissionIdentity to the self[attr].
   *
   * @param mixed $attr
   *   Name of array to add the identities to (mappings, members, wellKnowns).
   * @param array[PermissionIdentityExpansion]|PermissionIdentityExpansion $p_PermissionIdentities
   *   List of PermissionIdentityExpansion to add.
   */
  function __add(&$attr, $p_PermissionIdentities) {
    // Check if correct
    if ($p_PermissionIdentities == NULL || empty($p_PermissionIdentities)) {
      return;
    }
    if (!is_array($p_PermissionIdentities)) {
      $p_PermissionIdentities = array($p_PermissionIdentities);
    }
    if (!is_a($p_PermissionIdentities[0], 'Coveo\\Search\\SDK\\SDKPushPHP\\PermissionIdentityExpansion')) {
      //Error( "_add: value is not of type PermissionIdentityExpansion");
      return;
    }    $attr = array_merge($attr, $p_PermissionIdentities);
  }

  /**
   * Add member.
   *
   * @param array[\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion]|\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion $p_PermissionIdentities
   *   List of PermissionIdentityExpansion to add.
   */
  function AddMembers($p_PermissionIdentities) {
    //Debug('AddMembers');
    $this->__add($this->members, $p_PermissionIdentities);
  }

  /**
   * Add list of permission identities to Mappings.
   *
   * @param array[\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion]|\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion $p_PermissionIdentities
   * List of PermissionIdentityExpansion to add.
   */
  function AddMappings($p_PermissionIdentities) {
    //Debug('AddMappings');
    $this->__add($this->mappings, $p_PermissionIdentities);
  }

  /**
   * Add list of permission identities to Well Knowns.
   *
   * @param array[\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion]|\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion $p_PermissionIdentities
   *   List of PermissionIdentityExpansion to add.
   */
  function AddWellKnowns($p_PermissionIdentities) {
    //Debug('AddWellKnowns');
    $this->__add($this->wellKnowns, $p_PermissionIdentities);
  }

}
