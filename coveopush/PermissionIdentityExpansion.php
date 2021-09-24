<?php
// -------------------------------------------------------------------------------------
// CoveoPermissions
// -------------------------------------------------------------------------------------
// Contains the Permissions which are used inside the CoveoDocument
//   PermissionSets, PermisionLevels and Permissions
// -------------------------------------------------------------------------------------
namespace Coveo\Search\SDK\SDKPushPHP;
/**
 * Class PermissionIdentityExpansion.
 * Class to hold the Permission Identity for expansion.
 *  identityType (User, Group, Virtual Group ==> PermissionIdentityType),
 *  identity (for example: *@* or peter@coveo.com),
 *  securityProvider (for example: Confluence Provider).
 */
class PermissionIdentityExpansion{

    // The identityType/Type (User, Group or Virtual Group).
    // PermissionIdentityType
    public $type = '';

    // The associated identity provider identifier.
    // By default, if no securityProvider is specified, the identity will be associated the default
    // securityProvider/Provider defined in the configuration.
    public $provider = '';

    // The identity/name provided by the identity provider to identify the permission identity.
    public $name = '';

    // The additional information is a collection of key value pairs that
    // can be used to uniquely identify the permission identity.
    public $additionalInfo = array();

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function __construct( string $p_IdentityType, string $p_SecurityProvider, string $p_Identity,array $p_AdditionalInfo=null){
        /*"""
        class PermissionIdentity constructor.
        :arg p_IdentityType: PermissionIdentityType.
        :arg p_SecurityProvider: Security Provider name
        :arg p_Identity: Identity to add
        :arg p_AdditionalInfo: AdditionalInfo dict {} to add
        """*/
        if ($p_AdditionalInfo==null) {
            $p_AdditionalInfo = array();
        }
        $this->name = $p_Identity;
        $this->provider = $p_SecurityProvider;
        $this->type = $p_IdentityType;
        $this->additionalInfo = $p_AdditionalInfo;
    }
}
