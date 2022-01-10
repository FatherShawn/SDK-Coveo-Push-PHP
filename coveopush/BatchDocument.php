<?php

namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class to hold the Batch Document.
 */
class BatchDocument {
  /**
   * Documents to add or update.
   *
   * @var array
   */
  public $AddOrUpdate = array();

  /**
   * Documents to delete.
   *
   * @var array
   */
  public $Delete = array();

}
