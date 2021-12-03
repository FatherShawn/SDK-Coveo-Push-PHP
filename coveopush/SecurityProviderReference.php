<?php

namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * SecurityProviderReference
 */
class SecurityProviderReference {
  /**
   * Undocumented variable
   *
   * @var string
   */
  public $id = '';

  /**
   * Undocumented variable
   *
   * @var string
   */
  public $type = 'SOURCE';

  /**
   * Default constructor used by the deserialization.
   *
   * @param string $p_SourceId
   * @param string $p_type
   */
  function __construct(string $p_SourceId, string $p_type) {
    $this->id = $p_SourceId;
    $this->type = $p_type;
  }

}

