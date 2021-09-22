<?php
// -------------------------------------------------------------------------------------
// SecurityProviderReference
// -------------------------------------------------------------------------------------

namespace Coveo\Search\SDK\SDKPushPHP;

class SecurityProviderReference{
    public $id = '';
    public $type = 'SOURCE';
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // Default constructor used by the deserialization.

    function __construct(string $p_SourceId, string $p_type){
        /*"""
        Constructor SecurityProviderReference.
        :arg p_SourceId: Source id.
        :arg p_type: "SOURCE"
        """*/
        $this->id = $p_SourceId;
        $this->type = $p_type;
    }
}
