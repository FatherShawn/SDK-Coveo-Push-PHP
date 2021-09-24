<?php
// -------------------------------------------------------------------------------------
// CoveoPermissions
// -------------------------------------------------------------------------------------
// Contains the Permissions which are used inside the CoveoDocument
//   PermissionSets, PermisionLevels and Permissions
// -------------------------------------------------------------------------------------
namespace Coveo\Search\SDK\SDKPushPHP;

use Coveo\Search\SDK\SDKPushPHP\Enum;
use Coveo\Search\SDK\SDKPushPHP\Constants;
use Coveo\Search\Api\Service\LoggerInterface;

/**
 * Class DocumentPermissionSet.
 * Class to hold one Permission Set.
 */
class DocumentPermissionSet{

    // The name of the permission set.
    public $Name = '';

    // Whether to allow anonymous access to the document or not.
    public $AllowAnonymous = False;

    // The allowed permissions. List of PermissionIdentity
    public $AllowedPermissions = array();

    // The denied permissions. List of PermissionIdentity
    public $DeniedPermissions = array();

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // Default constructor used by the deserialization.
    function __construct( string $p_Name){
        $this->Name = $p_Name;
        $this->AllowAnonymous = False;
        $this->AllowedPermissions = array();
        $this->DeniedPermissions = array();
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function AddAllowedPermissions( $p_PermissionIdentities) {
        /*"""
        AddAllowedPermissions.
        Add a list of PermissionIdentities to the AllowedPermissions
        :arg p_PermissionIdentities: list of PermissionIdentity.
        """*/
        //Debug('AddAllowedPermissions');
        // Check if correct
        if ($p_PermissionIdentities==null || empty($p_PermissionIdentities)) {
            return;
        }

        if (!is_array($p_PermissionIdentities)){
            $p_PermissionIdentities = array($p_PermissionIdentities);
        }
        if (!is_a($p_PermissionIdentities[0], 'Coveo\\SDK\\SDKPushPHP\\PermissionIdentity')){
            //Error( "AddAllowedPermissions: value is not of type PermissionIdentity");
            return;
        }


        $this->AllowedPermissions = array_merge( $this->AllowedPermissions, $p_PermissionIdentities);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function AddDeniedPermissions( $p_PermissionIdentities){
        /*"""
        AddDeniedPermissions.
        Add a list of PermissionIdentities to the DeniedPermissions
        :arg p_PermissionIdentities: list of PermissionIdentity.
        """*/
        //Debug('AddDeniedPermissions');
        // Check if correct
        if ($p_PermissionIdentities==null || empty($p_PermissionIdentities)) {
            return;
        }

        if (!is_array($p_PermissionIdentities)){
            $p_PermissionIdentities = array($p_PermissionIdentities);
        }
        if (!is_a($p_PermissionIdentities[0], 'Coveo\\SDK\\SDKPushPHP\\PermissionIdentity')){
            //Error( "AddDeniedPermissions: value is not of type PermissionIdentity");
            return;
        }


        $this->DeniedPermissions = array_merge( $this->DeniedPermissions, $p_PermissionIdentities);
    }
}
