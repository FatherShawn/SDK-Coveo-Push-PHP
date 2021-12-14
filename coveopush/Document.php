<?php

namespace Coveo\Search\SDK\SDKPushPHP;

use \DateTime;
use Coveo\Search\SDK\SDKPushPHP\DefaultLogger;
use Exception;

/**
 * Class to hold the Document To Push. Mandatory properties: DocumentId (URL) and Title.
 */
class Document {
  public $Data = '';
  public $Date = '';
  public $DocumentId = '';
  public $permanentid = '';
  public $Title = '';
  public $ModifiedDate = '';
  public $CompressedBinaryData = '';
  public $CompressedBinaryDataFileId = '';
  public $CompressionType = '';
  public $FileExtension = '';
  public $ParentId = '';
  public $ClickableUri = '';
  public $Author = '';
  public $Permissions = array();
  public $MetaData = array();

  /**
   * Logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Document class constructor.
   *
   * @param string $p_DocumentId
   *   Valid URL.
   * @param [type] $logger
   *   Logger.
   */
  function __construct(string $p_DocumentId, $logger = NULL) {
    $this->DocumentId = $p_DocumentId;
    $this->permanentid = $this->hashdoc($p_DocumentId);
    $this->Permissions = array();
    $this->MetaData = array();
    $this->Data = '';
    $this->Date = '';
    $this->Title = '';
    $this->ModifiedDate = '';
    $this->CompressedBinaryData = '';
    $this->CompressedBinaryDataFileId = '';
    $this->CompressionType = '';
    $this->FileExtension = '';
    $this->ParentId = '';
    $this->ClickableUri = '';
    $this->Author = '';
    $this->logger = $logger ?? new DefaultLogger();
  }

  /**
   * Hash document.
   *
   * @param string $documentId
   *   The document Id.
   *
   * @return string|false
   *   Hash value of $documentId.
   */
  function hashdoc($documentId) {
    $hash_object = hash('sha256', utf8_encode($documentId));
    return $hash_object;
  }

  /**
   * Check if string is base64 encoded.
   *
   * @param string $s
   *   The processed string.
   *
   * @return bool
   *   If true the string is base 64 encoded.
   */
  function isBase64($s) {
    try {
      return base64_encode(base64_decode($s)) == $s;
    }
    catch (Exception $e) {
      return FALSE;
    }
  }

  /**
   * Validates if all properties on the Coveo Document are properly set.
   *
   * @return bool|Exception
   */
  function Validate() {
    $result = TRUE;
    $error = array();
    if ($this->permanentid == '') {
      array_push($error, 'permanentid is empty');
      $result = FALSE;
    }
    if ($this->DocumentId == '') {
      array_push($error, 'DocumentId is empty');
      $result = FALSE;
    }
    // data or CompressedBinaryData should be set, not both
    if ($this->Data && $this->CompressedBinaryData) {
      array_push($error, 'Both Data and CompressedBinaryData are set');
      $result = FALSE;
    }
    // Validate documentId, should be a valid url
    try {
      $parsed_url = parse_url($this->DocumentId);

      if (!$parsed_url["scheme"]) {
        array_push($error, 'DocumentId is not a valid URL format [missing scheme]: ' . $this->DocumentId);
        $result = FALSE;
      }

      if (!$parsed_url["path"]) {
        array_push($error, 'DocumentId is not a valid URL format [missing path]: ' . $this->DocumentId);
        $result = FALSE;
      }
    }
    catch (Exception $e) {
      array_push($error, 'DocumentId is not a valid URL format:' . $this->DocumentId);
      $result = FALSE;
    }
    if (count($error) > 0) {
      $this->logger->info('[Validate Document] ' . $this->DocumentId . implode(' | ', $error));
    }
    else {
      $this->logger->info('Document ' . $this->DocumentId . ' is valid.');
    }
    return array($result, implode(' | ', $error));
  }

  /**
   * Puts all metadata and other fields into a clean JSON object.
   *
   * @return string|bool
   *   A JSON encoded string on success or FALSE on failure.
   */
  function ToJson() {
    $attributes = array(
      'DocumentId', 'permanentid', 'Title', 'ClickableUri',
      'Data', 'CompressedBinaryData', 'CompressedBinaryDataFileId', 'CompressionType',
      'Date', 'ModifiedDate',
      'FileExtension',
      'ParentId',
      'Author', 'Permissions'
    );

    $all = array();
    foreach ($attributes as &$attr) {
      //echo $attr;
      if (property_exists("Coveo\Search\\SDK\\SDKPushPHP\\Document", $attr)) {
        //echo $attr;
        if (is_array($this->{$attr})) {
          if (count($this->{$attr}) > 0) {
            $all[$attr] = $this->{$attr};
          }
        }
        else {
          if ($this->{$attr} != "") {
            $all[$attr] = $this->{$attr};
          }
        }
      }
    }
    foreach ($this->MetaData as $key => $value) {
      $all[$key] = $value;
    }
    //echo json_encode($all);
    return json_encode($all);
  }

  /**
   * Puts all metadata and other fields into a clean JSON object.
   *
   * @return string|bool
   *   A JSON encoded string on success or FALSE on failure.
   */
  function cleanUp() {
    $attributes = array(
      'DocumentId', 'permanentid', 'Title', 'ClickableUri',
      'Data', 'CompressedBinaryData', 'CompressedBinaryDataFileId', 'CompressionType',
      'Date', 'ModifiedDate',
      'FileExtension',
      'ParentId',
      'Author', 'Permissions'
    );

    $all = array();
    foreach ($attributes as &$attr) {
      if (property_exists("Coveo\Search\\SDK\\SDKPushPHP\\Document", $attr)) {
        if (is_array($this->{$attr})) {
          if (count($this->{$attr}) > 0) {
            $all[$attr] = $this->{$attr};
          }
        }
        else {
          if ($this->{$attr} != "") {
            $all[$attr] = $this->{$attr};
          }
        }
      }
    }
    foreach ($this->MetaData as $key => $value) {
      $all[$key] = $value;
    }
    //echo json_encode($all);
    $source = json_encode($all);
    //Debug($source);
    $result = preg_replace('/,\s*"[^"]+":null|"[^"]+":null,?/', '', $source);
    $result = preg_replace('/,\s*"[^"]+":\[\]|"[^"]+":\[\],?/', '', $source);
    //Debug($result);
    //return $result;
    return json_decode($result);
  }

  /**
   * Sets the Data (plain text) property.
   *
   * @param string $p_Data
   *   The data.
   */
  function SetData(string $p_Data) {
    //Debug('SetData');
    // Check if empty
    if ($p_Data == '') {
      $this->logger->error('[Setdata] : value not set');
      return;
    }
    $this->Data = $p_Data;
  }

  /**
   * Sets the date property.
   *
   * @param \DateTime $p_Date
   *   The date.
   */
  function SetDate(DateTime $p_Date) {
    // if string, parse it into a datetime
    if (is_string($p_Date)) {
      $p_Date = date(DATE_ISO8601, strtotime($p_Date));
    }

    // Check we have a datetime object
    if (!is_a($p_Date, 'DateTime')) {
      $this->logger->error("SetDate: invalid datetime object");
      return;
    }

    $this->Date = $p_Date->format(DateTime::ATOM);
  }

  /**
   * Sets the ModifiedDate property.
   *
   * @param \DateTime $p_Date
   *   The value for ModifiedDate.
   */
  function SetModifiedDate(DateTime $p_Date) {
    // if string, parse it into a datetime
    if (is_string($p_Date)) {
      $p_Date = date(DATE_ISO8601, strtotime($p_Date));
    }

    // Check we have a datetime object
    if (!is_a($p_Date, 'DateTime')) {
      $this->logger->error("SetModifiedDate: invalid datetime object");
      return;
    }

    $this->ModifiedDate = $p_Date->format(DateTime::ATOM);
  }

  /**
   * Sets the CompressedBinaryData property.Make sure to set the proper CompressionType and Base64 encode the CompressedEncodedData.
   *
   * @param string $p_CompressedEncodedData
   *   Encoded Data (base64 ecoded).
   * @param \Coveo\Search\SDK\SDKPushPHP\CompressionType $p_CompressionType
   *   (def: ZLIB), CompressionType to Use.
   */
  function SetCompressedEncodedData(string $p_CompressedEncodedData, $p_CompressionType = NULL) {
    if ($p_CompressionType == NULL) {
      $this->p_CompressionType = CompressionType::ZLIB;
    }

    $this->logger->debug('SetCompressedEncodedData');
    // Check if empty
    if ($p_CompressedEncodedData == '') {
      $this->logger->error("SetCompressedEncodedData: value not set");
      return;
    }

    // Check if base64 encoded
    if (!$this->isBase64($p_CompressedEncodedData)) {
      $this->logger->error("SetCompressedEncodedData: value must be base64 encoded.");
      return;
    }

    $this->CompressedBinaryData = $p_CompressedEncodedData;
    $this->CompressedBinaryDataFileId = '';
    $this->CompressionType = $p_CompressionType;
  }

  /**
   * Sets the CompressedBinaryData property, it will ZLIB compress the string and base64 encode it.
   *
   * @param string $p_Content
   *   Content to be pushed.
   */
  function SetContentAndZLibCompress(string $p_Content) {
    // $this->logger->debug('SetContentAndZLibCompress');
    // Check if empty
    if ($p_Content == '') {
      $this->logger->error("SetContentAndZLibCompress: value not set");
      return;
    }
    $compresseddata = gzcompress($p_Content, 9);
    $encodeddata = base64_encode($compresseddata);

    $this->CompressedBinaryData = $encodeddata;
    $this->CompressedBinaryDataFileId = '';
    $this->CompressionType = CompressionType::ZLIB;
  }

  /**
   * Gets the file, compresses it (ZLIB), base64 encode it, set the filetype.
   *
   * @param string $p_FilePath
   *   Valid file path.
   */
  function GetFileAndCompress(string $p_FilePath) {
    $this->logger->debug('GetFileAndCompress ');
    $this->logger->debug($p_FilePath);
    // Check if empty
    if ($p_FilePath == '') {
      $this->logger->error("GetFileAndCompress: value not set");
      return;
    }

    // Check if file exists
    if (!file_exists($p_FilePath)) {
      $this->logger->error("GetFileAndCompress: file does not exists " . $p_FilePath);
      return;
    }

    $filecontent = file_get_contents($p_FilePath);
    $compresseddata = gzcompress($filecontent, 9);
    $encodeddata = base64_encode($compresseddata);

    $this->CompressedBinaryData = $encodeddata;
    $this->CompressedBinaryDataFileId = '';
    $this->CompressionType = CompressionType::ZLIB;
    $this->FileExtension = pathinfo($p_FilePath, PATHINFO_EXTENSION);
  }

  /**
   * Sets the CompressedBinaryDataFileId property.
   *
   * @param string $p_CompressedDataFileId
   *   The fileId retrieved by the GetLargeFileContainer call.
   */
  function SetCompressedDataFileId(string $p_CompressedDataFileId) {
    $this->logger->debug('SetCompressedDataFileId ');
    $this->logger->debug($p_CompressedDataFileId);
    // Check if empty
    if ($p_CompressedDataFileId == '') {
      $this->logger->error("SetCompressedDataFileId: value not set");
      return;
    }

    $this->CompressedBinaryData = '';
    $this->Data = '';
    $this->CompressedBinaryDataFileId = $p_CompressedDataFileId;
  }

  /**
   * Sets the metadata.
   *
   * @param string $p_Key
   *   The key value to set.
   * @param object $p_Value
   *   The value or object to set (str or list).
   */
  function AddMetadata(string $p_Key, $p_Value) {
    $this->logger->debug('AddMetadata');
    //Debug($p_Key . ": " . mb_convert_encoding(utf8_encode($p_Value), "UTF-8", "ASCII"));
    // Check if empty
    if ($p_Key == '') {
      $this->logger->error("AddMetadata: key not set");
      return;
    }

    // Check if in reserved keys
    $lower = strtolower($p_Key);
    if (array_key_exists($lower, Constants::S_DOCUMENT_RESERVED_KEYS)) {
      $this->logger->error("AddMetadata: " . $p_Key . " is a reserved field and cannot be set as metadata.");
      return;
    }

    // Check if empty
    if ($p_Value == '' || $p_Value == NULL) {
      $this->logger->warning("AddMetadata: value not set");
      return;
    }
    else {
      $this->MetaData[$lower] = $p_Value;
    }

  }

  /**
   * Sets the permissions on the document.
   *
   * @param array $p_AllowedPermissions
   *   List of PermissionIdentities which have access.
   * @param array $p_DeniedPermissions
   *   list of PermissionIdentities which do NOT have access.
   * @param [type] $p_AllowAnonymous
   *   (def: FALSE) if Anonymous access is allowed.
   */
  function SetAllowedAndDeniedPermissions(array $p_AllowedPermissions, array $p_DeniedPermissions, bool $p_AllowAnonymous = NULL) {
    if ($p_AllowAnonymous == NULL) {
      $p_AllowAnonymous = FALSE;
    }

    $this->logger->debug('SetAllowedAndDeniedPermissions');
    // Check if empty
    if ($p_AllowedPermissions == NULL) {
      $this->logger->error("SetAllowedAndDeniedPermissions: AllowedPermissions not set");
      return;
    }
    $simplePermissionLevel = new DocumentPermissionLevel('Level1');

    $simplePermissionSet = new DocumentPermissionSet('Set1');
    $simplePermissionSet->AddAllowedPermissions($p_AllowedPermissions);
    $simplePermissionSet->AddDeniedPermissions($p_DeniedPermissions);
    $simplePermissionSet->AllowAnonymous = $p_AllowAnonymous;

    $simplePermissionLevel->AddPermissionSet($simplePermissionSet);

    array_push($this->Permissions, $simplePermissionLevel);
  }

}
