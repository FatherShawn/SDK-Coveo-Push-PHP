<?php
// -------------------------------------------------------------------------------------
// CoveoPermissions
// -------------------------------------------------------------------------------------
// Contains the Permissions which are used inside the CoveoDocument
//   PermissionSets, PermisionLevels and Permissions
// -------------------------------------------------------------------------------------
namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class DocumentPermissionLevel.
 * Class to hold one Permission Level.
 */
class DocumentPermissionLevel{

    // The name of the permission level.
    public $Name = '';

    // The permission sets. Points to DocumentPermissionSet
    public $PermissionSets = array();

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // Default constructor used by the deserialization.
    function __construct( string $p_Name){
        $this->Name = $p_Name;
        $this->PermissionSets = array();
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function AddPermissionSet(DocumentPermissionSet $p_DocumentPermissionSet){
        /*"""
        AddPermissionSet.
        Add a DocumentPermissionSet to the current Level.
        :arg p_DocumentPermissionSet: DocumentPermissionSet.
        """*/
        //Debug('AddPermissionSet');
        // Check if correct
        if (!is_a($p_DocumentPermissionSet, 'Coveo\\SDK\\SDKPushPHP\\DocumentPermissionSet')){
            //Error( "AddPermissionSet: value is not of type DocumentPermissionSet");
            return;
        }

        array_push( $this->PermissionSets, $p_DocumentPermissionSet);
    }
}
