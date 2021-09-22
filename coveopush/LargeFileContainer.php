<?php
// -------------------------------------------------------------------------------------
// LargeFileContainer
// -------------------------------------------------------------------------------------
// Contains the LargeFileContainer class
// -------------------------------------------------------------------------------------
namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class to store the properties returned by LargeFile Container call
 * The secure URI used to upload the item data into an Amazon S3 file.
 */
class LargeFileContainer{

    public $UploadUri = '';

    // The file identifier used to link the uploaded data to the pushed item.
    // This value needs to be set in the item 'CompressedBinaryDataFileId' metadata.
    public $FileId = '';

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // Default constructor used by the deserialization.
    function __construct( array $p_JSON){
        $this->UploadUri = $p_JSON['uploadUri'];
        $this->FileId = $p_JSON['fileId'];
    }
}
