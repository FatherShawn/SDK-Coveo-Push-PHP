<?php
// -------------------------------------------------------------------------------------
// CoveoConstants
// -------------------------------------------------------------------------------------
// Contains the SourceStatusType Constants used by the SDK
// -------------------------------------------------------------------------------------

namespace Coveo\Search\SDK\SDKPushPHP;

use Coveo\Search\SDK\SDKPushPHP\Enum as Enum;

class SourceStatusType extends Enum{
    const Rebuild = "REBUILD";
    const Refresh = "REFRESH";
    const Incremental = "INCREMENTAL";
    const Idle = "IDLE";
}
