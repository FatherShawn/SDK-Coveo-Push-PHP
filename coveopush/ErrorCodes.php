<?php
// -------------------------------------------------------------------------------------
// ErrorCodes
// -------------------------------------------------------------------------------------
// Contains the ErrorCodes used by the SDK
// -------------------------------------------------------------------------------------

namespace Coveo\Search\SDK\SDKPushPHP;

class ErrorCodes{
    const Codes = array(
    "429" => "Too many requests. Slow down your pushes! Are you using Batch Calls?",
    "413" => "Request too large. The document is too large to be processed. It should be under 5 mb.",
    "412" => "Invalid or missing parameter - invalid source id",
    "403" => "Access Denied. Validate that your API Key has the proper access and that your Org and Source Id are properly specified",
    "401" => "Unauthorized or invalid token. Ensure your API key has the appropriate permissions.",
    "400" => "Organization is Paused. Reactivate it OR Invalid JSON",
    "504" => "Timeout"
    );
}
