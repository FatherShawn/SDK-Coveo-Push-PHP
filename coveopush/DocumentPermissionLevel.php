<?php

namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class to hold one Permission Level. Contains the Permissions which are used inside the CoveoDocument.
 */
class DocumentPermissionLevel {

  /**
   *  The name of the permission level.
   *
   * @var string
   */
  public $Name = '';

  /**
   * The permission sets. Points to DocumentPermissionSet.
   *
   * @var array
   */
  public $PermissionSets = array();

  /**
   * Default constructor used by the deserialization.
   *
   * @param string $p_Name
   *   Permission name.
   */
  function __construct(string $p_Name) {
    $this->Name = $p_Name;
    $this->PermissionSets = array();
  }

  /**
   * Add a DocumentPermissionSet to the current Level.
   *
   * @param DocumentPermissionSet $p_DocumentPermissionSet
   *   DocumentPermissionSet.
   */
  function AddPermissionSet(DocumentPermissionSet $p_DocumentPermissionSet) {
    //Debug('AddPermissionSet');
    // Check if correct
    if (!is_a($p_DocumentPermissionSet, 'Coveo\\SDK\\SDKPushPHP\\DocumentPermissionSet')) {
      //Error( "AddPermissionSet: value is not of type DocumentPermissionSet");
      return;
    }
    array_push($this->PermissionSets, $p_DocumentPermissionSet);
  }

}
