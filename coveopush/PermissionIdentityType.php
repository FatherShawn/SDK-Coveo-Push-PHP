<?php

namespace Coveo\Search\SDK\SDKPushPHP;

use Coveo\Search\SDK\SDKPushPHP\Enum as Enum;

/**
 *  Contains the PermissionIdentityType used by the SDK.
 */
class PermissionIdentityType extends Enum {
  /**
   * Represents a standard, or undefined identity.
   */
  const Unknown = "UNKNOWN";

  /**
   * Represents a 'User' identity.
   */
  const User = "USER";

  /**
   * Represents a 'Group' identity.
   */
  const Group = "GROUP";

  /**
   * Represents a 'VirtualGroup' identity.
   */
  const VirtualGroup = "VIRTUAL_GROUP";

}
