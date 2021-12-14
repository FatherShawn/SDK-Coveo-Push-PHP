<?php
// -------------------------------------------------------------------------------------
// SecurityProvider
// -------------------------------------------------------------------------------------

namespace Coveo\Search\SDK\SDKPushPHP;

class SecurityProvider {
  public $name = '';
  public $nodeRequired = False;
  public $type = '';
  public $referencedBy = array();
  public $cascadingSecurityProviders = array();
}
