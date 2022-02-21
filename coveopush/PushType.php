<?php

namespace Coveo\Search\SDK\SDKPushPHP;

use Coveo\Search\SDK\SDKPushPHP\Enum as Enum;

/**
 * Contains the PushType used by the SDK.
 */
class PushType extends Enum {
  const PUSH = "PUSH";
  const STREAM = "STREAM";
  const UPDATE_STREAM = "UPDATE_STREAM";

}