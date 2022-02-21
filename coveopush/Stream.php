<?php

namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class to store the properties returned by Stream Container call. The secure URI used to upload the item data into an Amazon S3 file.
 */
class Stream {

  /**
   * Upload Uri.
   *
   * @var string
   */
  public $UploadUri = '';

  /**
   * The file identifier used to link the uploaded data to the pushed item.This value needs to be set in the item 'CompressedBinaryDataFileId' metadata.
   *
   * @var string
   */
  public $FileId = '';

  /**
   * The stream identifier used to link the uploaded data to the pushed item and the current stream.
   *
   * @var string
   */
  public $StreamId = '';

 /**
  * Default constructor used by the deserialization.
  *
  * @param array $p_JSON
  */
  function __construct() {
    $this->UploadUri = '';
    $this->FileId = '';
    $this->StreamId = '';
  }

}
