<?php

namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class to hold the Batch Document.
 */
class BatchPermissions {
  /**
   * PermissionIdentityBody.
   *
   * @var array
   */
  public $mappings = array();

  /**
   * PermissionIdentityBody.
   *
   * @var array
   */
  public $members = array();

  /**
   * PermissionIdentityBody.
   *
   * @var array
   */
  public $deleted = array();

  /**
   * Default constructor BatchPermissions used by the deserialization.
   */
  function __construct() {
    $this->mappings = array();
    $this->members = array();
    $this->deleted = array();
  }

  /**
   * Add a list of p_PermissionIdentityBodies to self[attr].
   *
   * @param mixed $attr
   *   name of array to add the identities to (mappings, members, wellKnowns).
   * @param array[\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityBody] | \Coveo\Search\SDK\SDKPushPHP\PermissionIdentityBody $p_PermissionIdentityBodies
   *   PermissionIdentityExpansion.
   */
  function __add(&$attr, $p_PermissionIdentityBodies) {
    // Check if correct
    if ($p_PermissionIdentityBodies == NULL || empty($p_PermissionIdentityBodies)) {
      return;
    }

    if (!is_array($p_PermissionIdentityBodies)) {
      $p_PermissionIdentityBodies = array($p_PermissionIdentityBodies);
    }
    if (!is_a($p_PermissionIdentityBodies[0], 'Coveo\\Search\\SDK\\SDKPushPHP\\PermissionIdentityBody')) {
      //Error( "_add: value is not of type PermissionIdentityBody");
      return;
    }
    $attr = array_merge($attr, $p_PermissionIdentityBodies);
  }

  /**
   * Add Members.
   *
   * @param array[\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityBody]|\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityBody $p_PermissionIdentityBodies
   *   Permission Identity Body(s).
   */
  function AddMembers($p_PermissionIdentityBodies) {
    //Debug('AddMembers Batch');
    $this->__add($this->members, $p_PermissionIdentityBodies);
  }

  /**
   * Add Mappings.
   *
   * @param array[\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityBody]|\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityBody $p_PermissionIdentityBodies
   *   Permission Identity Body(s).
   */
  function AddMappings($p_PermissionIdentityBodies) {
    //Debug('AddMappings Batch');
    $this->__add($this->mappings, $p_PermissionIdentityBodies);
  }

  /**
   * Add Deletes.
   *
   * @param array[\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityBody]|\Coveo\Search\SDK\SDKPushPHP\PermissionIdentityBody $p_PermissionIdentityBodies
   *   Permission Identity Body(s).
   */
  function AddDeletes($p_PermissionIdentityBodies) {
    //Debug('AddDeletes Batch');
    $this->__add($this->deleted, $p_PermissionIdentityBodies);
  }

}
