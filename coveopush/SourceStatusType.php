<?php

namespace Coveo\Search\SDK\SDKPushPHP;

use Coveo\Search\SDK\SDKPushPHP\Enum as Enum;

/**
 * Contains the SourceStatusType Constants used by the SDK.
 */
class SourceStatusType extends Enum {
  const Rebuild = "REBUILD";
  const Refresh = "REFRESH";
  const Incremental = "INCREMENTAL";
  const Idle = "IDLE";

}
