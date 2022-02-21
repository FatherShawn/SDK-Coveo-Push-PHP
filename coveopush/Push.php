<?php

namespace Coveo\Search\SDK\SDKPushPHP;

use Coveo\Search\SDK\SDKPushPHP\Constants;
use Coveo\Search\SDK\SDKPushPHP\PushType;
use Coveo\Search\SDK\SDKPushPHP\Stream;
use Coveo\Search\SDK\SDKPushPHP\Document;
use Coveo\Search\SDK\SDKPushPHP\DefaultLogger;
use Exception;

/**
 * Class Push.
 *   Holds all methods to start pushing data.
 *   3 methods of pushing data:
 *   A) Push a single document
 *       Usage: When you simply need push a single document once in a while
 *      NOT TO BE USED: When you need to update a lot of documents. Use Method C or Method B for that.
 *      require_once('../coveopush/CoveoConstants.php');
 *      require_once('../coveopush/CoveoDocument.php');
 *      require_once('../coveopush/CoveoPermissions.php');
 *      require_once('../coveopush/CoveoPush.php');
 *      require_once('../coveopush/Enum.php')
 *
 *       $sourceId = 'xx';
 *       $orgId = 'xx';
 *       $apiKey = 'xx';
 *
 *      Setup the push client
 *      $push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);
 *
 *      $push->UpdateSourceStatus(Coveo\SDKPushPHP\SourceStatusType::Rebuild);
 *
 *      Create a document
 *      $mydoc = new Coveo\SDKPushPHP\Document("https://myreference/doc2");
 *      $mydoc->SetData("This is document Two");
 *      $mydoc->FileExtension = ".html";
 *      $mydoc->AddMetadata("authors", "jdst@coveo.com");
 *      $mydoc->Author = "Wim";
 *      $mydoc->Title = "What's up Doc 2?";
 *      Push the document
 *      $push->AddSingleDocument($mydoc);
 *
 *    B) Push a batch of documents in a single call
 *       Usage: When you need to upload a lot of (smaller) documents
 *       NOT TO BE USED: When you need to update a lot of LARGE documents. Use Method C for that.*
 *
 *        Setup the push client
 *        $push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);
 *        Create a batch of documents
 *        $batch=array(
 *            createDoc('/testfiles/BigExample.pdf'),
 *            createDoc('/testfiles/BigExample2.pptx'));
 *
 *       Push the documents
 *        $push->AddDocuments($batch, array(), $updateSourceStatus, $deleteOlder);
 *
 *
 *   C) RECOMMENDED APPROACH: Push a batch of documents, document by document
 *       Usage: When you need to upload a lot of smaller/and or larger documents
 *       NOT TO BE USED: When you have a single document. Use Method A for that.
 *
 *        Setup the push client
 *        $push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);
 *        Start the batch
 *        $push->Start($updateSourceStatus, $deleteOlder);
 *        Set the maximum
 *        $push->SetSizeMaxRequest(150*1024*1024);
 *
 *        $push->Add(createDoc('/testfiles/Large1.pptx', '1'));
 *        $push->Add(createDoc('/testfiles/Large2.pptx', '1'));
 *        $push->Add(createDoc('/testfiles/Large1.pptx', '2'));
 *        $push->Add(createDoc('/testfiles/Large2.pptx', '2'));
 *        $push->Add(createDoc('/testfiles/Large1.pptx', '3'));
 *        $push->Add(createDoc('/testfiles/Large2.pptx', '3'));
 *        $push->Add(createDoc('/testfiles/Large1.pptx', '4'));
 *        $push->Add(createDoc('/testfiles/Large2.pptx', '4'));
 *        $push->Add(createDoc('/testfiles/Large1.pptx', '5'));
 *        $push->Add(createDoc('/testfiles/Large2.pptx', '5'));
 *
 *       # End the Push
 *        $push->End($updateSourceStatus, $deleteOlder);
 */
class Push {
  /**
   * Push source Id.
   *
   * @var string
   */
  public $SourceId = '';

  /**
   * Organization name.
   *
   * @var string
   */
  public $OrganizationId = '';

  /**
   * Api key.
   *
   * @var string
   */
  public $ApiKey = '';

  /**
   * Push API endpoint URL.
   *
   * @var string
   */
  public $Endpoint = PushApiEndpoint::PROD_PUSH_API_URL;

  /**
   * Push API type.
   *
   * @var string
   */
  public $PushType = PushType::PUSH;

  /**
   * Processing Delay In Minutes.
   *
   * @var int
   */
  public $ProcessingDelayInMinutes = 0;

  /**
   * Start Ordering Id.
   *
   * @var int
   *
   * @see https://docs.coveo.com/en/147/index-content/about-the-orderingid-parameter
   */
  public $StartOrderingId = 0;

  /**
   * Total batch document size.
   *
   * @var int
   */
  public $totalSize = 0;

  /**
   * Documents to add.
   *
   * @var array
   */
  public $ToAdd = array();

  /**
   * Documents to delete.
   *
   * @var array
   */
  public $ToDel = array();

  /**
   * Batch Permissions.
   *
   * @var \coveo\Search\SDK\SDKPushPHP\BatchPermissions
   */
  public $BatchPermissions;

  /**
   * Current Stream.
   *
   * @var \coveo\Search\SDK\SDKPushPHP\Stream
   */
  public $CurrentStream;

  /**
   * Max Request Size.
   *
   * @var int
   */
  public $MaxRequestSize = 0;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Default constructor used by the deserialization.
   *
   * @param string $p_SourceId
   *   Push source Id.
   * @param string $p_OrganizationId
   *   Prganization Id.
   * @param string $p_ApiKey
   *   Push Source Api Key.
   * @param [type] $p_Endpoint
   *   Push source endpoint URL.
   * @param [pushType] $p_PushType
   *   Push source endpoint URL.
   * @param \Psr\Log\LoggerInterface|NULL $logger
   *   Logger.
   */
  function __construct(string $p_SourceId, string $p_OrganizationId, string $p_ApiKey, string $p_Endpoint = NULL, $logger = NULL, $p_PushType = NULL) {
    set_time_limit(3000);
    $p_Endpoint = $p_Endpoint ?? PushApiEndpoint::PROD_PUSH_API_URL;
    $p_PushType = $p_PushType ?? PushType::PUSH;

    $this->SourceId = $p_SourceId;
    $this->OrganizationId = $p_OrganizationId;
    $this->ApiKey = $p_ApiKey;
    $this->Endpoint = $p_Endpoint;
    $this->PushType = $p_PushType;
    $this->MaxRequestSize = 255052544;
    $this->CurrentStream = new Stream();
    $this->logger = $logger ?? new DefaultLogger();
    // validate Api Key
    $valid = preg_match('/^\w{10}-\w{4}-\w{4}-\w{4}-\w{12}$/', $p_ApiKey, $matches);
    if ($valid == 0) {
      $this->logger->error("Invalid Api Key format");
      return;
    }

    $this->logger->info('Pushing to source ' . $p_SourceId);
    $this->logger->info('Using Push Type   ' . $p_PushType);
  }

  /**
   * Clean JSON.
   *
   * @param mixed $json
   *   The json object.
   *
   * @return string
   *   Return clean encoded json.
   */
  function cleanJSON($json) {
    $source = json_encode($json);
    //$this->logger->debug($source);
    $result = preg_replace('/,\s*"[^"]+": ?null|"[^"]+": ?null,?/', '', $source);
    $result = preg_replace('/,\s*"[^"]+": ?\[\]|"[^"]+": ?\[\],?/', '', $result);
    //$this->logger->debug($result);
    return $result;
  }

  /**
   * Set Max Size Request. By default MAXIMUM_REQUEST_SIZE_IN_BYTES is used (256 Mb).
   *
   * @param int $p_Max
   *   Max request size in bytes.
   */
  function SetSizeMaxRequest(int $p_Max) {
    if ($p_Max > Constants::MAXIMUM_REQUEST_SIZE_IN_BYTES) {
      $this->logger->error("SetSizeMaxRequest: to big");
      return FALSE;
    }
    $this->MaxRequestSize = $p_Max;
    return TRUE;
  }

  /**
   * Get Size Max Request.
   *
   * @return int
   *   The max size value.
   */
  function GetSizeMaxRequest() {
    if ($this->MaxRequestSize > 0) {
      return $this->MaxRequestSize;
    }
    return Constants::MAXIMUM_REQUEST_SIZE_IN_BYTES;
  }

  /**
   * Gets the Request headers needed for every Push call.
   *
   * @return array
   *   The request headers.
   */
  function GetRequestHeaders() {
    // $this->logger->debug('GetRequestHeaders');
    $content = array();
    $content['Authorization'] = 'Bearer ' . $this->ApiKey;
    $content['Content-Type'] = 'application/json';
    return ($content);
  }

  /**
   *  Gets the Request headers needed for calls to Amazon S3.
   *
   * @return array
   *   The request headers.
   */
  function GetRequestHeadersForS3() {
    // $this->logger->debug('GetRequestHeadersForS3');
    $content = array();
    $content['Content-Type'] = 'application/octet-stream';
    $content[HttpHeaders::AMAZON_S3_SERVER_SIDE_ENCRYPTION_NAME] = HttpHeaders::AMAZON_S3_SERVER_SIDE_ENCRYPTION_VALUE;

    return ($content);
  }

  /**
   * Create url.
   *
   * @param string $myEndpoint
   *   Endpoint URL.
   *
   * @return array
   *   Values of Push source API.
   */
  function createPath($myEndpoint = NULL) {
    $values = array();
    $values['endpoint'] = $myEndpoint ?? $this->Endpoint;
    $values['org_id'] = $this->OrganizationId;
    $values['src_id'] = $this->SourceId;
    $values['prov_id'] = '';
    $values['stream_id'] = '';
    return $values;
  }

  /**
   * Replace Path.
   *
   * @param string $path
   *   The path.
   * @param array $values
   *   Values to create the path with.
   *
   * @return string
   *   The new path.
   */
  protected function replacePath(string $path, array $values) {
    $newpath = $path;
    $origin = array("{endpoint}", "{org_id}", "{src_id}", "{prov_id}", "{stream_id}");
    $to   = array($values['endpoint'],$values['org_id'],$values['src_id'],$values['prov_id'], $values['stream_id']);

    $newpath = str_replace($origin, $to, $newpath);
    return $newpath;
  }

  /**
   * Get the URL to update the Status of the source call.
   *
   * @return string
   *   The url to update the push source status.
   */
  function GetStatusUrl() {
    $values = $this->createPath();
    $url = $this->replacePath(PushApiPaths::SOURCE_ACTIVITY_STATUS, $values);
    return $url;
  }

  /**
   * Create an Ordering Id, used to set the order of the pushed items.
   *
   * @return float
   *   The batch ordering Id.
   */
  function CreateOrderingId() {
    $ordering_id = round((microtime(true) * 1000), 0);
    return $ordering_id;
  }

  /**
   * Get the URL for the Large File Container call.
   *
   * @return string
   *   Url for large File container.
   */
  function GetLargeFileContainerUrl() {
    $values = $this->createPath();
    $url = $this->replacePath(PushApiPaths::DOCUMENT_GET_CONTAINER, $values);
    return $url;
  }

  /**
   * Get the URL for the Open Stream call.
   *
   * @return string
   *   Url for open stream.
   */
  function GetOpenStreamUrl() {
    $values = $this->createPath();
    $url = $this->replacePath(PushApiPaths::SOURCE_STREAM_OPEN, $values);
    return $url;
  }

  /**
   * Get the URL for the Close Stream call.
   * @param string $p_StreamId
   *   The streamId.
   *
   * @return string
   *   Url for close stream.
   */
  function GetCloseStreamUrl(string $p_StreamId) {
    $values = $this->createPath();
    $values['stream_id'] = $p_StreamId;
    $url = $this->replacePath(PushApiPaths::SOURCE_STREAM_CLOSE, $values);
    return $url;
  }

  /**
   * Get the URL for the Chunk Stream call.
   * @param string $p_StreamId
   *   The streamId.
   *
   * @return string
   *   Url for chunk stream.
   */
  function GetChunkStreamUrl(string $p_StreamId) {
    $values = $this->createPath();
    $values['stream_id'] = $p_StreamId;
    $url = $this->replacePath(PushApiPaths::SOURCE_STREAM_CHUNK, $values);
    return $url;
  }

   /**
   * Get the URL for the Update Stream call.
   *
   * @return string
   *   Url for update stream.
   */
  function GetUpdateStreamUrl() {
    $values = $this->createPath();
    $url = $this->replacePath(PushApiPaths::SOURCE_STREAM_UPDATE, $values);
    return $url;
  }
  /**
   * Get the URL for the Update Document call.
   *
   * @return string
   *   The Document url.
   */
  function GetUpdateDocumentUrl() {
    $values = $this->createPath();
    $url = $this->replacePath(PushApiPaths::SOURCE_DOCUMENTS, $values);
    return $url;
  }

  /**
   * Get the URL to create the security provider.
   *
   * @param string $p_Endpoint
   *   Push API endpoint.
   * @param string $p_SecurityProviderId
   *   SecurityProviderId.
   *
   * @return string
   *   Url to get security provider.
   */
  function GetSecurityProviderUrl(string $p_Endpoint, string $p_SecurityProviderId) {
    $values = $this->createPath($p_Endpoint);
    $values['prov_id'] = $p_SecurityProviderId;
    $url = $this->replacePath(PlatformPaths::CREATE_PROVIDER, $values);
    return $url;
  }

  /**
   * Get the URL for the Delete Document call.
   *
   * @return string
   *   Url to delete document.
   */
  function GetDeleteDocumentUrl() {
    $values = $this->createPath();
    $url = $this->replacePath(PushApiPaths::SOURCE_DOCUMENTS, $values);
    return $url;
  }

  /**
   * Get the URL for the Update Documents (batch) call.
   *
   * @return string
   *   Url to update document.
   */
  function GetUpdateDocumentsUrl() {
    $values = $this->createPath();
    $url = $this->replacePath(PushApiPaths::SOURCE_DOCUMENTS_BATCH, $values);
    return $url;
  }

  /**
   * Get the URL for the Delete Older Than call.
   *
   * @return string
   *   URL to get documents older than
   */
  function GetDeleteOlderThanUrl() {
    $values = $this->createPath();
    $url = $this->replacePath(PushApiPaths::SOURCE_DOCUMENTS_DELETE, $values);
    return $url;
  }

  /**
   * Return path with values (endpoint, org, source, provider) set accordingly.
   *
   * @param string $path
   *   Path.
   * @param string $prov_id
   *   Provider Id.
   *
   * @return string
   *   Path.
   */
  function GetUrl(string $path, string $prov_id = '') {
    $values = $this->createPath();
    $values['prov_id'] = $prov_id;
    $url = $this->replacePath($path, $values);
    return $url;
  }

  /**
   * Checks the return code of the response (from the request object).If not valid an error will be raised.
   *
   * @param mixed $p_Response
   *   Response from request.
   *
   * @return string
   *   The status code value.
   *
   * @see https://docs.coveo.com/en/95/index-content/troubleshooting-push-api-error-codes
   */
  function CheckReturnCode($p_Response) {
    $this->logger->debug($p_Response['status_code']);
    if ($p_Response['status_code'] == 403) {
      $this->logger->error('Check privileges on your Api key.');
      return;
    }

    if ($p_Response['status_code'] >= 300) {
      $this->logger->debug($p_Response['text']);
      return;
    }
    //p_Response.raise_for_status()
    return $p_Response['status_code'];
  }

  /**
   * Array to string.
   *
   * @param array $process
   *   The array of values.
   *
   * @return string
   *   String of values.
   */
  function arrayToStr(array $process) {
    $newstring = '';
    foreach ($process as $key => $value) {
      $newstring .= $key . ':' . $value . "\r\n";
    }
    return $newstring;
  }

  /**
   * Do post request.
   *
   * @param string $url
   *   Request URL.
   * @param array $headers
   *   Request headers.
   * @param array $postdata
   *   Post params.
   *
   * @return mixed
   *   The json response.
   */
  function doPost($url, $headers, array $postdata) {
    $headers = array_merge($headers, array('Connection' => 'close'));
    $opts = array('http' =>
      array(
        'method'  => 'POST',
        'header'  => $this->arrayToStr($headers),
      )
    );
    if ($postdata !== NULL) {
      $params = http_build_query($postdata);
      //$opts['http']['content'] = $params;
      $url .= '?' . $params;
    }

    $context = stream_context_create($opts);
    $result = file_get_contents(($url), FALSE, $context);
    if ($result === FALSE) {
      $this->logger->error('POST Request failed: ' . $url . ' /n' . json_encode($params));
      return FALSE;
    }
    else {
      $this->logger->debug('POST Request succeeded: ' . $url . ' /n' . json_encode($params));
      $json = json_decode($result, TRUE);
      return $json;
    }
  }

  /**
   * Do put request.
   *
   * @param string $url
   *   Request url.
   * @param array $headers
   *   Request headers.
   * @param [type] $data
   *   Request data.
   * @param [type] $params
   *   Request parameters.
   *
   * @return mixed
   *   The json response.
   */
  function doPut($url, $headers, $data, $params = NULL) {
    $headers = array_merge($headers, array('Connection' => 'close'));
    $opts = array('http' =>
      array(
        'method'  => 'PUT',
        'header'  => $this->arrayToStr($headers),
      )
    );
    if ($data !== NULL) {
      $opts['http']['content'] = $data;
    }
    if ($params !== NULL) {
      $params = http_build_query($params);
      $url .= '?' . $params;
    }
    $context = stream_context_create($opts);
    //echo($url);
    //echo(json_decode($opts));
    $result = file_get_contents(($url), FALSE, $context);
    if ($result === FALSE) {
      $this->logger->error('PUT Request failed: ' . $url);
      return FALSE;
    }
    else {
      $this->logger->debug('PUT Request succeeded: ' . $url);
      $json = json_decode($result, TRUE);
      return $json;
    }
  }

  /**
   * Do Delete request.
   *
   * @param string $url
   *   Request url.
   * @param array $headers
   *   Request headers.
   * @param object|array $params
   *   Request parameters.
   * @param mixed $data
   *   Request data.
   *
   * @return mixed
   *   The json response.
   */
  function doDelete($url, $headers, $params = NULL, $data = NULL) {
    $headers = array_merge($headers, array('Connection' => 'close'));

    $opts = array('http' =>
      array(
          'method'  => 'DELETE',
          'header'  => $this->arrayToStr($headers)
      )
    );
    if ($data !== NULL) {
      $opts['http']['content'] = $data;
    }
    if ($params !== NULL) {
      $params = http_build_query($params);
      $url .= '?' . $params;
    }
    $context = stream_context_create($opts);

    $result = file_get_contents(($url), FALSE, $context);
    //echo 'doDelete';
    if ($result === FALSE) {
      $this->logger->error('DELETE Request failed: ' . $url);
      return FALSE;
    }
    else {
      $this->logger->debug('DELETE Request succeeded: ' . $url);
      //echo json_decode($result, TRUE);
      $json = json_decode($result, TRUE);
      return $json;
    }
  }

  /**
   * Update the Source status, so that the activity on the source reflects what is going on.
   *
   * @param string $p_SourceStatus
   *   Constants.SourceStatusType (REBUILD, IDLE).
   *
   * @return bool
   *   True if source srarus is updated. false if it failed to update.
   */
  function UpdateSourceStatus(string $p_SourceStatus) {
    if ($this->PushType === PushType::PUSH) {
      $params = array(Parameters::STATUS_TYPE => $p_SourceStatus);

      $result = $this->doPost($this->GetStatusUrl(), $this->GetRequestHeaders(), $params);
      if ($result != FALSE) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    } else {
      //With Stream not allowed to update the source status
      return TRUE;
    }
  }

  /**
   * Get the S3 Large Container information.
   *
   * @return \Coveo\Search\SDK\SDKPushPHP\LargeFileContainer|null
   *   LargeFileContainer Class.
   */
  function GetLargeFileContainer() {
    $params = array();
    $url = $this->GetLargeFileContainerUrl();
    $result = $this->doPost($url, $this->GetRequestHeaders(), $params);
    if ($result !== FALSE) {
      $results = new LargeFileContainer($result);
      return $results;
    }
    else {
      return NULL;
    }
  }

   /**
   * Get the S3 Stream Container information.
   *
   * @return \Coveo\Search\SDK\SDKPushPHP\StreamFileContainer|null
   *   StreamFileContainer Class.
   */
  function GetStreamFileContainer() {
    $params = array();
    $url = $this->GetOpenStreamUrl();
    $result = $this->doPost($url, $this->GetRequestHeaders(), $params);
    if ($result !== FALSE) {
      $results = new StreamFileContainer($result);
      return $results;
    }
    else {
      return NULL;
    }
  }

   /**
   * Get the S3 Stream Chunk Container information.
   *
   * @param string $p_StreamId
   *   The streamId.

   * @return \Coveo\Search\SDK\SDKPushPHP\LargeFileContainer|null
   *   LargeFileContainer Class.
   */
  function GetStreamChunkFileContainer(string $p_StreamId) {
    $params = array();
    $url = $this->GetChunkStreamUrl($p_StreamId);
    $result = $this->doPost($url, $this->GetRequestHeaders(), $params);
    if ($result !== FALSE) {
      $results = new LargeFileContainer($result);
      return $results;
    }
    else {
      return NULL;
    }
  }

  /**
   * Checks if string is base64 encoded.
   *
   * @param string $s
   *   The string to process.
   *
   * @return bool
   *   TRUE/FALSE if it is base 64 encoded.
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
   * Upload a document to S3.
   *
   * @param string $p_UploadUri
   *   Retrieved from the GetLargeFileContainer call.
   * @param string $p_CompressedFile
   *   Properly compressed file to upload as contents.
   *
   * @return bool
   *   true if document was uploaded, false if not.
   */
  function UploadDocument(string $p_UploadUri, string $p_CompressedFile) {

    if ($p_UploadUri === NULL) {
      $this->logger->error("UploadDocument: p_UploadUri is not present");
      return FALSE;
    }
    if ($p_CompressedFile === NULL) {
      $this->logger->error("UploadDocument: p_CompressedFile is not present");
      return FALSE;
    }

    // Check if p_CompressedFile is base64 encoded, if so, decode it first.
    if ($this->isBase64($p_CompressedFile)) {
      $p_CompressedFile = base64_decode($p_CompressedFile);
    }
    $result = $this->doPut($p_UploadUri, $this->GetRequestHeadersForS3(), $p_CompressedFile);
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }

  }

  /**
   * Upload a batch document to S3.
   *
   * @param string $p_UploadUri
   *   Retrieved from the GetLargeFileContainer call.
   * @param array $p_ToAdd
   *   List of CoveoDocuments to add.
   * @param array $p_ToDelete
   *   List of CoveoDocumentToDelete to delete.
   *
   * @return bool
   *   True if document was uploaded, false if not.
   */
  function UploadDocuments(string $p_UploadUri, array $p_ToAdd, array $p_ToDelete) {
    if ($p_UploadUri === NULL) {
      $this->logger->error("UploadDocument: p_UploadUri is not present");
      return FALSE;
    }
    if ($p_ToAdd === NULL && $p_ToDelete === NULL) {
      $this->logger->error("UploadBatch: p_ToAdd and p_ToDelete are empty");
      return FALSE;
    }

    $data = new BatchDocument();
    $data->AddOrUpdate = $p_ToAdd;
    $data->Delete = $p_ToDelete;

    $result = $this->doPut($p_UploadUri, $this->GetRequestHeadersForS3(), $this->cleanJSON($data));
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Upload a batch permission to S3.
   *
   * @param string $p_UploadUri
   *   Retrieved from the GetLargeFileContainer call.
   *
   * @return bool
   */
  function UploadPermissions(string $p_UploadUri) {
    if ($p_UploadUri === NULL) {
      $this->logger->error("UploadPermissions: p_UploadUri is not present");
      return FALSE;
    }

    $permissions = $this->cleanJSON($this->BatchPermissions);
    $result = $this->doPut($p_UploadUri, $this->GetRequestHeadersForS3(), $permissions);
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Get a Large File Container instance and Upload the document to S3.
   *
   * @param string $p_Content
   *   Properly compressed file to upload as contents.
   *
   * @return string
   *   S3 FileId value.
   */
  function GetContainerAndUploadDocument(string $p_Content) {
    $container = $this->GetLargeFileContainer();
    if ($container === NULL) {
      $this->logger->error("GetContainerAndUploadDocument: S3 container is null");
      return;
    }

    $this->UploadDocument($container->UploadUri, $p_Content);

    return $container->FileId;
  }

  /**
   * Uploads an Uncompressed/Compressed Document, if it is to large a S3 container is created, document is being uploaded to s3.
   *
   * @param \Coveo\Search\SDK\SDKPushPHP\Document $p_Document
   *   The Document to upload.
   */
  function UploadDocumentIfTooLarge(Document $p_Document) {
    $size = strlen($p_Document->Data) + strlen($p_Document->CompressedBinaryData);
    $this->logger->debug('UploadDocumentIfTooLarge documentId: ' . $p_Document->DocumentId . ' size = ' . $size);

    if ($size > Constants::COMPRESSED_DATA_MAX_SIZE_IN_BYTES) {
      $data = '';
      if ($p_Document->Data) {
        $data = $p_Document->Data;
      }
      else {
        $data = $p_Document->CompressedBinaryData;
      }

      $fileId = $this->GetContainerAndUploadDocument($data);
      $p_Document->SetCompressedDataFileId($fileId);
    }
  }

  /**
   * Sends the document to the Push API, if previously uploaded to s3 the fileId is set.
   *
   * @param \Coveo\Search\SDK\SDKPushPHP\Document $p_CoveoDocument
   *   The document to upload.
   * @param int|null $orderingId
   *   The ordering Id.
   *
   * @return bool
   *   True if document is uploaded, false if not.
   */
  function AddUpdateDocumentRequest(Document $p_CoveoDocument, int $orderingId = NULL) {
    $params = array(Parameters::DOCUMENT_ID => $p_CoveoDocument->DocumentId);

    if ($orderingId !== NULL) {
      $params[Parameters::ORDERING_ID] = $orderingId;
    }
    // Set the compression type parameter.
    if ($p_CoveoDocument->CompressedBinaryData != '' || $p_CoveoDocument->CompressedBinaryDataFileId != '') {
      $params[Parameters::COMPRESSION_TYPE] = $p_CoveoDocument->CompressionType;
    }

    $body = json_encode($p_CoveoDocument->cleanUp());
    // self.logger.debug(body)
    $result = $this->doPut($this->GetUpdateDocumentUrl(), $this->GetRequestHeaders(), $body, $params);
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Deletes the document.
   *
   * @param string $p_DocumentId
   *   Coveo Document id.
   * @param int|null $orderingId
   *   Ordering Id.
   * @param bool|null $deleteChildren
   *   If children must be deleted.
   *
   * @return bool
   *   True if the docuemnt is deleted. false if delete document request failed.
   */
  function DeleteDocument(string $p_DocumentId, int $orderingId = NULL, bool $deleteChildren = NULL) {
    $params = array(Parameters::DOCUMENT_ID => $p_DocumentId);

    if ($orderingId !== NULL) {
      $params[Parameters::ORDERING_ID] = $orderingId;
    }
    $deleteChildren = $deleteChildren ?? FALSE;
    if ($deleteChildren === TRUE) {
      $params[Parameters::DELETE_CHILDREN] = "true";
    }

    $result = $this->doDelete($this->GetDeleteDocumentUrl(), $this->GetRequestHeaders(), $params);
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * All documents with a smaller orderingId will be removed from the index.
   *
   * @param float|null $orderingId
   *   Ordering Id.
   * @param int|null $queueDelay
   *   Queue Delay.
   *
   * @return bool
   *   True if request seccessed, false if request failed.
   */
  function DeleteOlderThan(float $orderingId = NULL, int $queueDelay = NULL) {
     // Validate
    if ($orderingId <= 0) {
      $this->logger->error("DeleteOlderThan: orderingId must be a positive 64 bit float.");
      return FALSE;
    }
    $params = array( Parameters::ORDERING_ID => $orderingId);

    if ($queueDelay !== NULL) {
      if (!($queueDelay >= 0 && $queueDelay <= 1440)) {
        $this->logger->error("DeleteOlderThan: queueDelay must be between 0 and 1440.");
        return FALSE;
      }
      else {
        $params[Parameters::QUEUE_DELAY] = $queueDelay;
      }
    }
    else {
      $params[Parameters::QUEUE_DELAY] = 0;
    }
    $result = $this->doDelete($this->GetDeleteOlderThanUrl(), $this->GetRequestHeaders(), $params);
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Pushes the Document to the Push API.
   *
   * @param \Coveo\Search\SDK\SDKPushPHP\Document $p_CoveoDocument
   *   Coveo Document.
   * @param bool|null $updateStatus
   *   (True), if the source status should be updated.
   * @param int|null $orderingId
   *   Ordering Id.
   */
  function AddSingleDocument(Document $p_CoveoDocument, bool $updateStatus = NULL, int $orderingId = NULL) {
    // Single Call
    // First check.
    list($valid, $error) = $p_CoveoDocument->Validate();
    if (!$valid) {
      return FALSE;
    }
    $updateStatus = $updateStatus ?? TRUE;
    // Update Source Status.
    if ($updateStatus) {
      $this->UpdateSourceStatus(SourceStatusType::Rebuild);
    }
    // Push Document.
    try {
      if ($p_CoveoDocument->CompressedBinaryData !== '' || $p_CoveoDocument->Data !== '') {
        $this->UploadDocumentIfTooLarge($p_CoveoDocument);
      }
      $this->AddUpdateDocumentRequest($p_CoveoDocument, $orderingId);
    }
    finally{
      $p_CoveoDocument->Content = '';
    }
    // Update Source Status.
    if ($updateStatus) {
      $this->UpdateSourceStatus(SourceStatusType::Idle);
    }
  }

  /**
   * Deletes the CoveoDocument to the Push API.
   *
   * @param string $p_DocumentId
   *   Id of the document to delete.
   * @param bool|null $updateStatus
   *   (True), if the source status should be updated.
   * @param int|null $orderingId
   *   If not supplied a new one will be created.
   * @param bool|null $deleteChildren
   *   (FALSE), if children must be deleted.
   */
  function RemoveSingleDocument(string $p_DocumentId, bool $updateStatus = NULL, int $orderingId = NULL, bool $deleteChildren = NULL) {
    // Single Call
    // Update Source Status.
    $updateStatus = $updateStatus ?? TRUE;
    $is_source_updated = TRUE;
    if ($updateStatus) {
      $is_source_updated = $this->UpdateSourceStatus(SourceStatusType::Rebuild);
    }

    // Delete document.
    $is_document_delete = TRUE;
    $is_document_delete = $this->DeleteDocument($p_DocumentId, $orderingId, $deleteChildren);

    // Update Source Status.
    if ($updateStatus) {
      $is_source_updated = $is_source_updated && $this->UpdateSourceStatus(SourceStatusType::Idle);
    }
    return $is_document_delete && $is_source_updated;
  }

  /**
   * Sends the documents to the Push API, if previously uploaded to s3 the fileId is set.
   *
   * @param string $p_FileId
   *   File Id retrieved from GetLargeFileContainer call.
   *
   * @return bool
   *   Return TRUE or FALSE if request succeeded.
   */
  function AddUpdateDocumentsRequest(string $p_FileId) {
    $params = array(Parameters::FILE_ID => $p_FileId);
    // make PUT request to change status.
    $result = $this->doPut($this->GetUpdateDocumentsUrl(), $this->GetRequestHeaders(), NULL, $params);
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Sends the documents to the Stream API, if previously uploaded to s3 the fileId is set.
   *
   * @param string $p_FileId
   *   File Id retrieved from GetLargeFileContainer call.
   *
   * @return bool
   *   Return TRUE or FALSE if request succeeded.
   */
  function AddUpdateStreamRequest(string $p_FileId) {
    $params = array(Parameters::FILE_ID => $p_FileId);
    // make PUT request to change status.
    $result = $this->doPut($this->GetUpdateStreamUrl(), $this->GetRequestHeaders(), NULL, $params);
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Uploads the batch to S3 and calls the Push API to record the fileId.
   *
   * @param array $p_ToAdd
   *   list of CoveoDocuments to add.
   * @param array $p_ToDelete
   *   list of CoveoDocumentToDelete to delete.
   *
   */
  function UploadBatch(array $p_ToAdd, array $p_ToDelete) {
    if ($p_ToAdd === NULL && $p_ToDelete === NULL) {
      $this->logger->error("UploadBatch: p_ToAdd and p_ToDelete are empty");
      return FALSE;
    }
    if ($this->PushType === PushType::PUSH ) {
      $container = $this->GetLargeFileContainer();
      if ($container === NULL) {
        $this->logger->error("UploadBatch: S3 container is NULL");
        return FALSE;
      }
      $result = TRUE;

      $result = $this->UploadDocuments($container->UploadUri, $p_ToAdd, $p_ToDelete);

      $result = $result && $this->AddUpdateDocumentsRequest($container->FileId);
    }
    if ($this->PushType === PushType::STREAM ) {
      $result = TRUE;

      $result = $this->UploadDocuments($this->CurrentStream->UploadUri, $p_ToAdd, $p_ToDelete);
      $container = $this->GetStreamChunkFileContainer($this->CurrentStream->StreamId);
      if ($container === NULL) {
        $this->logger->error("UploadBatch: S3 container is NULL");
        return FALSE;
      }
      $this->CurrentStream->UploadUri = $container->UploadUri;
      $this->CurrentStream->FileId = $container->FileId;

    }
    if ($this->PushType === PushType::UPDATE_STREAM ) {
      $container = $this->GetLargeFileContainer();
      if ($container === NULL) {
        $this->logger->error("UploadBatch: S3 container is NULL");
        return FALSE;
      }
      $result = TRUE;

      $result = $this->UploadDocuments($container->UploadUri, $p_ToAdd, $p_ToDelete);

      $result = $result && $this->AddUpdateStreamRequest($container->FileId);
    }

    return $result;
  }

  /**
   * Will create batches of documents to push to S3 and to upload to the Push API.
   *
   * @param array $p_Documents
   *   List of CoveoDocument/CoveoDocumentToDelete to add/delete.
   */
  function ProcessAndUploadBatch(array $p_Documents) {
    $currentBatchToDelete = array();
    $currentBatchToAddUpdate = array();

    $totalSize = 0;
    $size_max_req = $this->GetSizeMaxRequest();
    foreach ($p_Documents as $document) {
      // Add 1 byte to account for the comma in the JSON array.
      // documentSize = len(json.dumps(document,default=lambda x: x.__dict__)) + 1
      $documentSize = strlen($document->ToJson()) + 1;

      $totalSize += $documentSize;
      $this->logger->debug("Document: " . $document->DocumentId . " Currentsize: " . $totalSize . " vs max: " . $size_max_req);

      if ($documentSize > $size_max_req) {
        $this->logger->error("Document: " . $document->DocumentId . " can\'t be larger than " . $size_max_req . " bytes in size.");
        return FALSE;
      }

      if ($totalSize > $size_max_req - (count($currentBatchToAddUpdate) + count($currentBatchToDelete))) {
        $this->UploadBatch($currentBatchToAddUpdate, $currentBatchToDelete);
        $currentBatchToAddUpdate = array();
        $currentBatchToDelete = array();
        $totalSize = $documentSize;
      }

      if (is_a($document, 'Coveo\\SDK\\SDKPushPHP\\DocumentToDelete')) {
        array_push($currentBatchToDelete, $document->cleanUp()); //->ToJson());
      }
      else {
        // Validate each document
        list($valid, $error) = $document->Validate();
        if ($valid === FALSE) {
          return;
        }
        else {
          array_push($currentBatchToAddUpdate, $document->cleanUp()); //.ToJson());
        }
      }
    }

    return $this->UploadBatch($currentBatchToAddUpdate, $currentBatchToDelete);
  }

  /**
   * Adds all documents in several batches to the Push API.
   *
   * @param array $p_CoveoDocumentsToAdd
   *   List of Coveo Document to add.
   * @param array $p_CoveoDocumentsToDelete
   *   List of CoveoDocument to delete.
   * @param bool|null $p_UpdateStatus
   *   (True), if the source status should be updated.
   * @param bool|null $p_DeleteOlder
   *   (FALSE), if older documents should be removed from the index after the new push.
   *
   * @return bool
   */
  function AddDocuments(array $p_CoveoDocumentsToAdd, array $p_CoveoDocumentsToDelete, bool $p_UpdateStatus = NULL, bool $p_DeleteOlder = NULL) {
    if ($p_CoveoDocumentsToAdd === NULL && $p_CoveoDocumentsToDelete === NULL) {
      $this->logger->error("AddDocuments: p_CoveoDocumentsToAdd and p_CoveoDocumentsToDelete is empty");
      return FALSE;
    }

    $p_UpdateStatus = $p_UpdateStatus ?? TRUE;
    $is_source_updated = TRUE;
    $is_stream_updated = TRUE;
    // Update Source Status
    if ($p_UpdateStatus) {
      $is_source_updated = $this->UpdateSourceStatus(SourceStatusType::Rebuild);
    }

    // Check mode
    if ($this->PushType === PushType::STREAM ) {
      $this->CurrentStream = $this->GetStreamFileContainer();
      if ($this->CurrentStream === NULL) {
        $this->logger->error("AddDocuments: S3 container is NULL");
        return FALSE;
      }
    }
    if ($this->PushType === PushType::UPDATE_STREAM ) {
      $this->CurrentStream = $this->GetLargeFileContainer();
      if ($this->CurrentStream === NULL) {
        $this->logger->error("AddDocuments: S3 container is NULL");
        return FALSE;
      }
    }
    // Push the Documents
    if (!empty($p_CoveoDocumentsToAdd)) {
      $allDocuments = $p_CoveoDocumentsToAdd;
    }

    if (!empty($p_CoveoDocumentsToDelete)) {
      $allDocuments = array_merge($allDocuments, $p_CoveoDocumentsToDelete);
    }

    $batch_uploaded = $this->ProcessAndUploadBatch($allDocuments);
    $p_DeleteOlder = $p_DeleteOlder ?? FALSE;

    // Close the stream
    if ($this->PushType === PushType::STREAM ) {
      $params = array();
      $is_stream_updated = $this->doPost($this->GetCloseStreamUrl($this->CurrentStream->StreamId), $this->GetRequestHeaders(), $params);
    }

    $is_deleted = TRUE;
    // Delete Older Documents.
    if ($p_DeleteOlder && $this->PushType === PushType::PUSH) {
      // Batch Call
      // First check
      $startOrderingId = $this->CreateOrderingId();
      $is_deleted = $this->DeleteOlderThan($startOrderingId);
    }

    // Update Source Status.
    if ($p_UpdateStatus) {
      $is_source_updated = $is_source_updated && $this->UpdateSourceStatus(SourceStatusType::Idle);
    }
    return $is_source_updated && $batch_uploaded && $is_deleted && $is_stream_updated;
  }

  /**
   * Starts a batch Push call, will set the start ordering Id and will update the status of the source.
   *
   * @param bool|null $p_UpdateStatus
   *   (True), if the source status should be updated.
   *
   * @return bool
   *   True if Push source status is set to Rebuild and ordering id is created.
   */
  function Start(bool $p_UpdateStatus = NULL) {
    $p_UpdateStatus = $p_UpdateStatus ?? TRUE;
    // Batch Call
    // First check.
    $this->StartOrderingId = $this->CreateOrderingId();
    $is_ordering_id_valid = $this->StartOrderingId > 0;
    $is_source_updated = TRUE;
    // Update Source Status.
    if ($p_UpdateStatus) {
      $is_source_updated = $this->UpdateSourceStatus(SourceStatusType::Rebuild);
    }

    // Check mode
    if ($this->PushType === PushType::STREAM ) {
      $this->CurrentStream = $this->GetStreamFileContainer();
      if ($this->CurrentStream === NULL) {
        $this->logger->error("AddDocuments: S3 container is NULL");
        return FALSE;
      }
    }
    if ($this->PushType === PushType::UPDATE_STREAM ) {
      $this->CurrentStream = $this->GetLargeFileContainer();
      if ($this->CurrentStream === NULL) {
        $this->logger->error("AddDocuments: S3 container is NULL");
        return FALSE;
      }
    }
    
    return $is_ordering_id_valid && $is_source_updated;
  }

  /**
   * Add a document to the batch call, if the buffer max is reached content is pushed.
   *
   * @param \Coveo\Search\SDK\SDKPushPHP\Document $p_CoveoDocument
   *   Coveo Document of CoveoDocumentToDelete.
   *
   * @return bool
   *   Return true/false if document nwas added to the batch or uploaded if current batch size exceeds max batch size.
   */
  function Add($p_CoveoDocument) {
    if ($p_CoveoDocument == NULL) {
      $this->logger->error("Add: p_CoveoDocument is empty");
      return FALSE;
    }

    $documentSize = strlen($p_CoveoDocument->ToJson()) + 1;
    $size_max_req = $this->GetSizeMaxRequest();
    $this->totalSize += $documentSize;

    if ($documentSize > $size_max_req) {
      $this->logger->error("Document: " . $p_CoveoDocument->DocumentId . " can\'t be larger than " . $size_max_req . " bytes in size.");
      return FALSE;
    }
    else {
      $this->logger->debug("Document: " . $p_CoveoDocument->DocumentId . " Currentsize: " . $this->totalSize . " vs max: " . $size_max_req);
    }

    if ($this->totalSize > $size_max_req - (count($this->ToAdd) + count($this->ToDel))) {
      $this->logger->debug("Uploading the batch because it exceeded the max size.");
      // upload batch.
      $this->UploadBatch($this->ToAdd, $this->ToDel);
      // reset current document stacks and total size.
      $this->ToAdd = array();
      $this->ToDel = array();
      $this->totalSize = $documentSize;
    }
    // add document to delete/add stack.
    if (is_a($p_CoveoDocument, 'Coveo\\SDK\\SDKPushPHP\\DocumentToDelete')) {
      array_push($this->ToDel, $p_CoveoDocument->cleanUp()); //->ToJson());
    }
    else {
      // Validate each document.
      list($valid, $error) = $p_CoveoDocument->Validate();
      if (!$valid) {
        return FALSE;
      }
      else {
        array_push($this->ToAdd, $p_CoveoDocument->cleanUp()); //->ToJson());
      }
    }
    return TRUE;
  }

  /**
   * Add a JSON to the batch call, if the buffer max is reached content is pushed.
   *
   * @param string $p_JSON
   *
   * @return bool
   *   Return true/false if document nwas added to the batch or uploaded if current batch size exceeds max batch size.
   */
  function AddJson($p_Json) {
    if ($p_Json == NULL) {
      $this->logger->error("AddJson: p_Json is empty");
      return FALSE;
    }

    $documentSize = strlen($p_Json) + 1;
    $size_max_req = $this->GetSizeMaxRequest();
    $this->totalSize += $documentSize;

    if ($documentSize > $size_max_req) {
      $this->logger->error("Document: " . $p_CoveoDocument->DocumentId . " can\'t be larger than " . $size_max_req . " bytes in size.");
      return FALSE;
    }
    else {
      $this->logger->debug("Document: " . $p_CoveoDocument->DocumentId . " Currentsize: " . $this->totalSize . " vs max: " . $size_max_req);
    }

    if ($this->totalSize > $size_max_req - (count($this->ToAdd) + count($this->ToDel))) {
      $this->logger->debug("Uploading the batch because it exceeded the max size.");
      // upload batch.
      $this->UploadBatch($this->ToAdd, $this->ToDel);
      // reset current document stacks and total size.
      $this->ToAdd = array();
      $this->ToDel = array();
      $this->totalSize = $documentSize;
    }
    
    array_push($this->ToAdd, json_decode($p_Json)); //->ToJson());
    return TRUE;
  }

  /**
   * Ends the batch call (when started with Start()). Will push the final batch, update the status and delete older documents.
   *
   * @param bool|null $p_UpdateStatus
   *   (True), if the source status should be updated.
   * @param bool|null $p_DeleteOlder
   *   (FALSE), if older documents should be removed from the index after the new push.
   */
  function End(bool $p_UpdateStatus = NULL, bool $p_DeleteOlder = NULL) {
    // Batch Call.

    $is_batch_uploaded = $this->UploadBatch($this->ToAdd, $this->ToDel);
    $p_DeleteOlder = $p_DeleteOlder ?? FALSE;
    $is_deleted = TRUE;
    $is_stream_updated = TRUE;
    // Close the stream
    if ($this->PushType === PushType::STREAM ) {
      $params = array();
      $is_stream_updated = $this->doPost($this->GetCloseStreamUrl($this->CurrentStream->StreamId), $this->GetRequestHeaders(), $params);
    }

    if ($p_DeleteOlder  && $this->PushType === PushType::PUSH) {
      // Delete Older Documents.
      $is_deleted = $this->DeleteOlderThan($this->StartOrderingId);
    }

    $this->ToAdd = array();
    $this->ToDel = array();
    // Update Source Status.
    $p_UpdateStatus = $p_UpdateStatus ?? TRUE;
    $is_source_updated = TRUE;
    if ($p_UpdateStatus) {
      $is_source_updated = $this->UpdateSourceStatus(SourceStatusType::Idle);
    }
    return $is_batch_uploaded &&  $is_deleted && $is_source_updated && $is_stream_updated;
  }

  /**
   * Add a single Permission Expansion (PermissionIdentityBody).
   *
   * @param string $p_SecurityProviderId
   *   Security Provider name and Id to use.
   * @param string $p_Type
   *   Type of provider, normally 'EXPANDED'.
   * @param array $p_CascadingTo
   *   Cascading To.
   * @param string $p_Endpoint
   *   Constants.PlatformEndpoint.
   *
   * @return bool
   *   True if request to add security providere succeeded.
   */
  function AddSecurityProvider(string $p_SecurityProviderId, string $p_Type, array $p_CascadingTo, string $p_Endpoint = NULL) {
    $secProvider = new SecurityProvider();
    $secProviderReference = new SecurityProviderReference($this->SourceId, "SOURCE");
    $secProvider->referencedBy = array($secProviderReference);
    $secProvider->name = $p_SecurityProviderId;
    $secProvider->type = $p_Type;
    $secProvider->nodeRequired = FALSE;
    $secProvider->cascadingSecurityProviders = $p_CascadingTo;

    // make PUT request to change status.
    $provider = $this->cleanJSON($secProvider);
    $p_Endpoint = $p_Endpoint ?? PlatformEndpoint::PROD_PLATFORM_API_URL;
    $result = $this->doPut($this->GetSecurityProviderUrl($p_Endpoint, $p_SecurityProviderId), $this->GetRequestHeaders(), $provider);
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Add a single Permission Expansion Call (PermissionIdentityBody).
   *
   * @param string $p_SecurityProviderId
   *   Security Provider to use.
   * @param \Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion $p_Identity
   *   PermissionIdentityExpansion.
   * @param array $p_Members
   *   list of PermissionIdentityExpansion.
   * @param array $p_Mappings
   *   list of PermissionIdentityExpansion.
   * @param array $p_WellKnowns
   *   list of PermissionIdentityExpansion.
   * @param int|null $orderingId
   *   ordering Id.
   *
   * @return bool
   *   True if request to expand permissions succeeded.
   */
  function AddPermissionExpansion(string $p_SecurityProviderId, PermissionIdentityExpansion $p_Identity, array $p_Members, array $p_Mappings, array $p_WellKnowns, int $orderingId = NULL) {
    $permissionIdentityBody = new PermissionIdentityBody($p_Identity);
    $permissionIdentityBody->AddMembers($p_Members);
    $permissionIdentityBody->AddMappings($p_Mappings);
    $permissionIdentityBody->AddWellKnowns($p_WellKnowns);

    $params = array();

    if ($orderingId !== NULL && $orderingId > 0 ) {
      $params[Parameters::ORDERING_ID] = $orderingId;
    }

    $resourcePathFormat = PushApiPaths::PROVIDER_PERMISSIONS;
    if ($p_Mappings !== NULL) {
      $resourcePathFormat = PushApiPaths::PROVIDER_MAPPINGS;
    }
    $resourcePath = $this->GetUrl($resourcePathFormat, $p_SecurityProviderId);

    $identity = $this->cleanJSON($permissionIdentityBody);

    $result = $this->doPut($resourcePath, $this->GetRequestHeaders(), $identity, $params);
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Will start a Batch for Expansion/Permission updates.
   * Using AddExpansionMember, AddExpansionMapping or AddExpansionDeleted operations are added.
   * EndExpansion must be called at the end to write the Batch to the Push API.
   */
  function StartExpansion() {
    $this->StartOrderingId = $this->CreateOrderingId();
    $this->BatchPermissions = new BatchPermissions();
  }

  /**
   * For example: GROUP has 3 members.Add a single Permission Expansion (PermissionIdentityBody) to the Members.
   *
   * @param \Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion $p_Identity
   *   PermissionIdentityExpansion, must be the same as Identity in PermissionIdentity when pushing documents.
   * @param array $p_Members
   *   list of PermissionIdentityExpansion.
   * @param array $p_Mappings
   *   list of PermissionIdentityExpansion.
   * @param array $p_WellKnowns
   *   list of PermissionIdentityExpansion.
   */
  function AddExpansionMember(PermissionIdentityExpansion $p_Identity, array $p_Members, array $p_Mappings, array $p_WellKnowns) {
    $permissionIdentityBody = new PermissionIdentityBody($p_Identity);
    $permissionIdentityBody->AddMembers($p_Members);
    $permissionIdentityBody->AddMappings($p_Mappings);
    $permissionIdentityBody->AddWellKnowns($p_WellKnowns);
    $this->BatchPermissions->AddMembers($permissionIdentityBody);
  }

  /**
   * For example: Identity WIM has 3 mappings: wim@coveo.com, w@coveo.com, ad\\w
   * Add a single Permission Expansion (PermissionIdentityBody) to the Mappings
   *
   * @param \Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion $p_Identity
   *   PermissionIdentityExpansion, must be the same as Identity in PermissionIdentity when pushing documents.
   * @param array $p_Members
   *   list of PermissionIdentityExpansion.
   * @param array $p_Mappings
   *   list of PermissionIdentityExpansion.
   * @param array $p_WellKnowns
   *   list of PermissionIdentityExpansion.
   */
  function AddExpansionMapping(PermissionIdentityExpansion $p_Identity, array $p_Members, array $p_Mappings, array $p_WellKnowns) {
    $permissionIdentityBody = new PermissionIdentityBody($p_Identity);
    $permissionIdentityBody->AddMembers($p_Members);
    $permissionIdentityBody->AddMappings($p_Mappings);
    $permissionIdentityBody->AddWellKnowns($p_WellKnowns);
    $this->BatchPermissions->AddMappings($permissionIdentityBody);
  }

  /**
   * Add a single Permission Expansion (PermissionIdentityBody) to the Deleted, will be deleted from the security cache.
   *
   * @param \Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion $p_Identity
   *   PermissionIdentityExpansion, must be the same as Identity in PermissionIdentity when pushing documents.
   * @param array $p_Members
   *   list of PermissionIdentityExpansion.
   * @param array $p_Mappings
   *   list of PermissionIdentityExpansion.
   * @param array $p_WellKnowns
   *   list of PermissionIdentityExpansion.
   */
  function AddExpansionDeleted(PermissionIdentityExpansion $p_Identity, array $p_Members, array $p_Mappings, array $p_WellKnowns) {
    $permissionIdentityBody = new PermissionIdentityBody($p_Identity);
    $permissionIdentityBody->AddMembers($p_Members);
    $permissionIdentityBody->AddMappings($p_Mappings);
    $permissionIdentityBody->AddWellKnowns($p_WellKnowns);
    $this->BatchPermissions->AddDeletes($permissionIdentityBody);
  }

  /**
   * Write the last batch of security updates to the push api.
   *
   * @param string $p_SecurityProviderId
   *   Security Provider to use.
   * @param bool|null $p_DeleteOlder
   *   (FALSE), if older documents should be removed from the index after the new push.
   */
  function EndExpansion(string $p_SecurityProviderId, bool $p_DeleteOlder = NULL) {
    $container = $this->GetLargeFileContainer();
    if ($container == NULL) {
      $this->logger->error("UploadBatch: S3 container is NULL");
      return;
    }
    $this->UploadPermissions($container->UploadUri);
    $params = array(Parameters::FILE_ID => $container->FileId);

    $resourcePath = $this->GetUrl(PushApiPaths::PROVIDER_PERMISSIONS_BATCH, $p_SecurityProviderId);

    $result = $this->doPut($resourcePath, $this->GetRequestHeaders(), NULL, $params);

    $p_DeleteOlder = $p_DeleteOlder ?? FALSE;
    if ($p_DeleteOlder) {
      $this->DeletePermissionsOlderThan($p_SecurityProviderId, $this->StartOrderingId);
    }
    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Remove a single Permission Mapping.
   *
   * @param string $p_SecurityProviderId
   *   Security Provider to use.
   * @param \Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion $p_PermissionIdentity
   *   PermissionIdentityExpansion, permissionIdentity to remove.
   */
  function RemovePermissionIdentity(string $p_SecurityProviderId, PermissionIdentityExpansion $p_PermissionIdentity) {
    $permissionIdentityBody = new PermissionIdentityBody($p_PermissionIdentity);

    $resourcePath = $this->GetUrl(PushApiPaths::PROVIDER_PERMISSIONS, $p_SecurityProviderId);
    $identity = $this->cleanJSON($permissionIdentityBody);

    $result = $this->doDelete($resourcePath, $this->GetRequestHeaders(), NULL, $identity);

    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Delete Permission Older Than, Deletes permissions older than orderingId.
   *
   * @param string $p_SecurityProviderId
   *   Security Provider to use.
   * @param int|null $orderingId
   *   the OrderingId to use.
   */
  function DeletePermissionsOlderThan(string $p_SecurityProviderId, int $orderingId = NULL) {
    if ($orderingId <= 0) {
      $this->logger->error("DeletePermissionsOlderThan: orderingId must be a positive 64 bit integer.");
      return FALSE;
    }

    $params = array(Parameters::ORDERING_ID => $orderingId);

    $resourcePath = $this->GetUrl(PushApiPaths::PROVIDER_PERMISSIONS_DELETE, $p_SecurityProviderId);

    $result = $this->doDelete($resourcePath, $this->GetRequestHeaders(), $params);

    if ($result !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

}
