<?php
// -------------------------------------------------------------------------------------
// PermissionIdentity
// -------------------------------------------------------------------------------------
// Contains the PermissionIdentity which are used inside the CoveoDocument
// -------------------------------------------------------------------------------------
namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class PermissionIdentity.
 * Class to hold the Permission Identity.
 * identityType (User, Group, Virtual Group ==> PermissionIdentityType),
 * identity (for example: *@* or peter@coveo.com),
 *  securityProvider (for example: Confluence Provider).
 */
class PermissionIdentity {

    // The identityType (User, Group or Virtual Group).
    // PermissionIdentityType
    public $identityType = '';

    // The associated identity provider identifier.
    // By default, if no securityProvider is specified, the identity will be associated the default
    // securityProvider defined in the configuration.
    public $securityProvider = '';

    // The identity provided by the identity provider to identify the permission identity.
    public $identity = '';

    // The additional information is a collection of key value pairs that
    // can be used to uniquely identify the permission identity.
    public $AdditionalInfo = array();

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
        $this->identity = $p_Identity;
        $this->securityProvider = $p_SecurityProvider;
        $this->identityType = $p_IdentityType;
        $this->AdditionalInfo = $p_AdditionalInfo;
    }
}
