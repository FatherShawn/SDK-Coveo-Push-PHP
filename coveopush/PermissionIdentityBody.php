<?php
// -------------------------------------------------------------------------------------
// CoveoPermissions
// -------------------------------------------------------------------------------------
// Contains the Permissions which are used inside the CoveoDocument
//   PermissionSets, PermisionLevels and Permissions
// -------------------------------------------------------------------------------------
namespace Coveo\Search\SDK\SDKPushPHP;
/**
 * Class PermissionIdentityBody.
 * Class to hold all associated Permission information for one Identity.
*/
class PermissionIdentityBody{

    // The identity.
    // The identity is represented by a Name, a Type (User, Group or Virtual Group) and its Addtionnal Info).
    // PermissionIdentity
    public $identity = '';

    // The mappings of a user.
    // Link different user identities in different systems that represent the same person.
    // For example:
    //     - corp\myuser (Active Directory)
    //     - myuser@myenterprise.com (Email)
    // List of PermissionIdentityExpansion
    public $mappings = array();

    // The members of a group or a virtual group (membership).
    // List of PermissionIdentityExpansion
    public $members = array();

    // The well-knowns.
    // Well-known is a group that identifies generic users or generic groups.
    // For example, in the Active Directory:
    // - Everyone: automatically includes everyone who uses the computer, even anonymous guests.
    // - Anonymous: automatically includes all users that have logged on anonymously.
    // List of PermissionIdentityExpansion
    public $wellKnowns = array();

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // Default constructor used by the deserialization.
    function __construct(PermissionIdentityExpansion $p_Identity){
        /*"""
        Constructor PermissionIdentityBody.
        :arg p_Identity: Identity name.
        """*/
        if (!is_a($p_Identity, 'Coveo\\SDK\\SDKPushPHP\\PermissionIdentityExpansion')){
            //Error("PermissionIdentityBody constructor: value is not of type PermissionIdentityExpansion");
            return;
        }

        $this->identity = $p_Identity;
        $this->mappings = array();
        $this->members = array();
        $this->wellKnowns = array();
    }

    function __add( &$attr,  $p_PermissionIdentities){
        /*"""
        Add.
        Add a PermissionIdentity to the self[attr]
        :arg attr: name of array to add the identities to (mappings, members, wellKnowns).
        :arg p_PermissionIdentity: PermissionIdentityExpansion.
        """*/
        // Check if correct
        if ($p_PermissionIdentities==null || empty($p_PermissionIdentities)) {
            return;
        }

        if (!is_array($p_PermissionIdentities)){
            $p_PermissionIdentities = array($p_PermissionIdentities);
        }
       // $type = ($p_PermissionIdentities[0] instanceof PermissionIdentityExpansion);
        //Debug(json_encode($p_PermissionIdentities));
        if (!is_a($p_PermissionIdentities[0], 'Coveo\\SDK\\SDKPushPHP\\PermissionIdentityExpansion')){
            //Error( "_add: value is not of type PermissionIdentityExpansion");
            return;
        }


        $attr = array_merge( $attr, $p_PermissionIdentities);
    }


    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function AddMembers( $p_PermissionIdentities){
      //Debug('AddMembers');
        $this->__add($this->members, $p_PermissionIdentities);
    }

    function AddMappings( $p_PermissionIdentities){
      //Debug('AddMappings');
        $this->__add($this->mappings, $p_PermissionIdentities);
    }

    function AddWellKnowns( $p_PermissionIdentities){
      //Debug('AddWellKnowns');
        $this->__add($this->wellKnowns, $p_PermissionIdentities);
    }
}
