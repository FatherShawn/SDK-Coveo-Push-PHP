<?php

/*"""
class BatchPermissions.
Class to hold the Batch Document.
"""*/
namespace Coveo\Search\SDK\SDKPushPHP;

class BatchPermissions{
    // PermissionIdentityBody
    public $mappings = array();
    // PermissionIdentityBody
    public $members = array();
    // PermissionIdentityBody
    public $deleted = array();

    // Default constructor used by the deserialization.
    function __construct(){
        /*"""
        Constructor BatchPermissions.
        """*/
        $this->mappings = array();
        $this->members = array();
        $this->deleted = array();
    }

    function __add( &$attr,  $p_PermissionIdentityBodies){
        /*"""
        Add.
        Add a list of p_PermissionIdentityBodies to self[attr].
        :arg attr: name of array to add the identities to (mappings, members, wellKnowns).
        :arg p_PermissionIdentity: PermissionIdentityExpansion.
        """*/
        // Check if correct
        if ($p_PermissionIdentityBodies==null || empty($p_PermissionIdentityBodies)) {
            return;
        }

        if (!is_array($p_PermissionIdentityBodies)){
            $p_PermissionIdentityBodies = array($p_PermissionIdentityBodies);
        }
        if (!is_a($p_PermissionIdentityBodies[0], 'Coveo\\SDK\\SDKPushPHP\\PermissionIdentityBody')){
            //Error( "_add: value is not of type PermissionIdentityBody");
            return;
        }


        $attr = array_merge( $attr, $p_PermissionIdentityBodies);
    }


    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function AddMembers( $p_PermissionIdentityBodies){
      //Debug('AddMembers Batch');
        $this->__add($this->members, $p_PermissionIdentityBodies);
    }

    function AddMappings( $p_PermissionIdentityBodies){
      //Debug('AddMappings Batch');
        $this->__add($this->mappings, $p_PermissionIdentityBodies);
    }

    function AddDeletes( $p_PermissionIdentityBodies){
      //Debug('AddDeletes Batch');
        $this->__add($this->deleted, $p_PermissionIdentityBodies);
    }
}
